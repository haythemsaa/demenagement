<?php
/**
 * Classe DemenageurMatcher
 *
 * Algorithme intelligent pour sélectionner les 3-4 déménageurs
 * les plus pertinents pour chaque demande de devis
 *
 * Critères de sélection:
 * 1. Zone géographique (distance, codes postaux couverts)
 * 2. Capacité et disponibilité
 * 3. Spécialités correspondantes
 * 4. Note et réputation
 * 5. Taux de réponse
 * 6. Niveau d'abonnement (priorité)
 */

class DemenageurMatcher {

    private $db;
    private $max_demenageurs = 4; // Nombre max de déménageurs à contacter

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Trouve les meilleurs déménageurs pour une demande de devis
     *
     * @param array $quote_request Données de la demande (postal_code_from, volume, services, etc.)
     * @return array Liste des déménageurs sélectionnés avec leur score
     */
    public function findBestMatches($quote_request) {
        $postal_code = $quote_request['postal_code_from'] ?? '';
        $departement = substr($postal_code, 0, 2);
        $volume = floatval($quote_request['total_volume'] ?? 0);
        $services = $quote_request['services'] ?? [];

        // 1. Récupérer tous les déménageurs actifs avec abonnement valide
        $candidates = $this->getActiveDemenageurs();

        if (empty($candidates)) {
            return [];
        }

        // 2. Calculer le score de pertinence pour chaque déménageur
        $scored_candidates = [];

        foreach ($candidates as $demenageur) {
            $score = $this->calculateMatchScore($demenageur, $quote_request);

            // Ne garder que les déménageurs ayant un score minimum
            if ($score >= 30) {
                $scored_candidates[] = [
                    'demenageur' => $demenageur,
                    'score' => $score,
                    'match_details' => $this->getMatchDetails($demenageur, $quote_request)
                ];
            }
        }

        // 3. Trier par score décroissant
        usort($scored_candidates, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 4. Retourner les N meilleurs
        return array_slice($scored_candidates, 0, $this->max_demenageurs);
    }

    /**
     * Récupère les déménageurs actifs avec abonnement valide
     */
    private function getActiveDemenageurs() {
        $sql = "
            SELECT
                d.*,
                sp.matching_priority,
                sp.name as plan_name,
                ds.leads_used_this_month,
                ds.plan_id,
                (sp.leads_per_month - ds.leads_used_this_month) as remaining_leads
            FROM demenageurs d
            INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
            INNER JOIN subscription_plans sp ON ds.plan_id = sp.id
            WHERE d.status = 'active'
                AND d.verified = TRUE
                AND ds.status = 'active'
                AND ds.end_date >= CURDATE()
                AND (
                    sp.leads_per_month = 0
                    OR ds.leads_used_this_month < sp.leads_per_month
                )
            ORDER BY sp.matching_priority ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule le score de pertinence (0-100)
     *
     * Pondération:
     * - Géographie: 40 points
     * - Abonnement/Priorité: 20 points
     * - Réputation: 15 points
     * - Capacité: 10 points
     * - Spécialités: 10 points
     * - Taux de réponse: 5 points
     */
    private function calculateMatchScore($demenageur, $quote_request) {
        $score = 0;

        // 1. GÉOGRAPHIE (40 points max)
        $score += $this->calculateGeographyScore($demenageur, $quote_request);

        // 2. ABONNEMENT/PRIORITÉ (20 points max)
        $score += $this->calculatePriorityScore($demenageur);

        // 3. RÉPUTATION (15 points max)
        $score += $this->calculateReputationScore($demenageur);

        // 4. CAPACITÉ (10 points max)
        $score += $this->calculateCapacityScore($demenageur, $quote_request);

        // 5. SPÉCIALITÉS (10 points max)
        $score += $this->calculateSpecialtyScore($demenageur, $quote_request);

        // 6. TAUX DE RÉPONSE (5 points max)
        $score += $this->calculateResponseScore($demenageur);

        return round($score, 2);
    }

    /**
     * Score géographique (0-40 points)
     */
    private function calculateGeographyScore($demenageur, $quote_request) {
        $postal_code = $quote_request['postal_code_from'] ?? '';
        $departement = substr($postal_code, 0, 2);

        $coverage_zones = json_decode($demenageur['coverage_zones'] ?? '[]', true);

        // Correspondance exacte du code postal (40 points)
        if (in_array($postal_code, $coverage_zones)) {
            return 40;
        }

        // Correspondance du département (35 points)
        if (in_array($departement, $coverage_zones)) {
            return 35;
        }

        // Calculer la distance si coordonnées disponibles
        if (!empty($demenageur['latitude']) && !empty($demenageur['longitude'])) {
            $distance = $this->calculateDistance(
                $quote_request['latitude'] ?? null,
                $quote_request['longitude'] ?? null,
                $demenageur['latitude'],
                $demenageur['longitude']
            );

            $max_distance = intval($demenageur['max_distance_km'] ?? 50);

            if ($distance !== null && $distance <= $max_distance) {
                // Score dégressif selon la distance
                // 0-10km: 30 points, 10-25km: 20 points, 25-50km: 10 points
                if ($distance <= 10) return 30;
                if ($distance <= 25) return 20;
                if ($distance <= 50) return 10;
            }
        }

        // Départements limitrophes (10 points)
        $nearby_depts = $this->getNearbyDepartements($departement);
        foreach ($coverage_zones as $zone) {
            if (in_array($zone, $nearby_depts)) {
                return 10;
            }
        }

        return 0;
    }

    /**
     * Score de priorité selon l'abonnement (0-20 points)
     */
    private function calculatePriorityScore($demenageur) {
        $priority = intval($demenageur['matching_priority'] ?? 5);

        // Conversion: priorité 1 = 20pts, priorité 2 = 16pts, ... priorité 5 = 4pts
        $score = max(0, 24 - ($priority * 4));

        return $score;
    }

    /**
     * Score de réputation (0-15 points)
     */
    private function calculateReputationScore($demenageur) {
        $rating = floatval($demenageur['average_rating'] ?? 0);
        $total_reviews = intval($demenageur['total_reviews'] ?? 0);

        // Note moyenne sur 5 = 10 points max
        $rating_score = ($rating / 5) * 10;

        // Bonus pour nombre d'avis (5 points max)
        // 0-10 avis: 0-2pts, 10-50 avis: 2-4pts, 50+ avis: 4-5pts
        $review_bonus = 0;
        if ($total_reviews >= 50) {
            $review_bonus = 5;
        } elseif ($total_reviews >= 10) {
            $review_bonus = 2 + (($total_reviews - 10) / 40) * 2;
        } elseif ($total_reviews > 0) {
            $review_bonus = ($total_reviews / 10) * 2;
        }

        return $rating_score + $review_bonus;
    }

    /**
     * Score de capacité (0-10 points)
     */
    private function calculateCapacityScore($demenageur, $quote_request) {
        $volume = floatval($quote_request['total_volume'] ?? 0);
        $fleet_size = intval($demenageur['fleet_size'] ?? 1);
        $staff_count = intval($demenageur['staff_count'] ?? 2);

        $score = 0;

        // Taille de la flotte
        if ($volume > 50 && $fleet_size >= 5) {
            $score += 5; // Gros volumes = grosse flotte
        } elseif ($volume > 20 && $fleet_size >= 3) {
            $score += 5;
        } elseif ($fleet_size >= 1) {
            $score += 3;
        }

        // Nombre d'employés
        if ($staff_count >= 10) {
            $score += 5;
        } elseif ($staff_count >= 5) {
            $score += 3;
        } elseif ($staff_count >= 2) {
            $score += 2;
        }

        return min(10, $score);
    }

    /**
     * Score de spécialité (0-10 points)
     */
    private function calculateSpecialtyScore($demenageur, $quote_request) {
        $requested_services = $quote_request['services'] ?? [];
        $specialties = json_decode($demenageur['specialties'] ?? '[]', true);
        $services_offered = json_decode($demenageur['services_offered'] ?? '[]', true);

        $score = 0;
        $matches = 0;

        // Vérifier correspondance des services
        foreach ($requested_services as $service) {
            if (in_array($service, $services_offered)) {
                $matches++;
            }
        }

        if (count($requested_services) > 0) {
            $score += ($matches / count($requested_services)) * 7;
        } else {
            $score += 5; // Score par défaut si pas de services spécifiques
        }

        // Bonus pour spécialités rares
        $rare_specialties = ['piano', 'oeuvres_art', 'international'];
        foreach ($specialties as $specialty) {
            if (in_array($specialty, $rare_specialties)) {
                $score += 1;
            }
        }

        return min(10, $score);
    }

    /**
     * Score de réactivité (0-5 points)
     */
    private function calculateResponseScore($demenageur) {
        $response_rate = floatval($demenageur['response_rate'] ?? 0);
        $avg_response_time = intval($demenageur['average_response_time'] ?? 48);

        // Taux de réponse (3 points max)
        $rate_score = ($response_rate / 100) * 3;

        // Temps de réponse (2 points max)
        // < 2h: 2pts, < 12h: 1.5pts, < 24h: 1pt, < 48h: 0.5pt
        $time_score = 0;
        if ($avg_response_time <= 2) {
            $time_score = 2;
        } elseif ($avg_response_time <= 12) {
            $time_score = 1.5;
        } elseif ($avg_response_time <= 24) {
            $time_score = 1;
        } elseif ($avg_response_time <= 48) {
            $time_score = 0.5;
        }

        return $rate_score + $time_score;
    }

    /**
     * Détails du matching pour affichage
     */
    private function getMatchDetails($demenageur, $quote_request) {
        return [
            'company_name' => $demenageur['company_name'],
            'rating' => $demenageur['average_rating'],
            'reviews_count' => $demenageur['total_reviews'],
            'response_rate' => $demenageur['response_rate'],
            'plan' => $demenageur['plan_name'],
            'city' => $demenageur['city'],
            'specialties' => json_decode($demenageur['specialties'] ?? '[]', true),
        ];
    }

    /**
     * Calcule la distance entre deux points GPS (formule de Haversine)
     * Retourne la distance en km
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }

        $earth_radius = 6371; // Rayon de la Terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earth_radius * $c;
    }

    /**
     * Retourne les départements limitrophes
     */
    private function getNearbyDepartements($dept) {
        $nearby_map = [
            '75' => ['92', '93', '94'],
            '92' => ['75', '78', '91', '93', '94'],
            '93' => ['75', '92', '94', '95'],
            '94' => ['75', '92', '93', '77', '91'],
            '69' => ['01', '42', '71', '38'],
            '13' => ['83', '84', '04', '05', '30'],
            '33' => ['40', '47', '24', '17'],
            // Ajouter d'autres mappings selon besoin
        ];

        return $nearby_map[$dept] ?? [];
    }

    /**
     * Envoie le lead aux déménageurs sélectionnés
     *
     * @param int $quote_request_id ID de la demande de devis
     * @param array $matched_demenageurs Déménageurs sélectionnés
     * @return array Résultat de l'envoi
     */
    public function sendLeadsToMatches($quote_request_id, $matched_demenageurs) {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($matched_demenageurs as $match) {
            $demenageur = $match['demenageur'];

            try {
                // 1. Créer l'entrée dans demenageur_leads
                $lead_id = $this->createLead($quote_request_id, $demenageur['id'], $match);

                // 2. Envoyer l'email de notification
                $email_sent = $this->sendLeadNotificationEmail($demenageur, $lead_id, $quote_request_id);

                // 3. Incrémenter le compteur de leads utilisés
                $this->incrementLeadsCounter($demenageur['id']);

                $results['sent']++;
                $results['details'][] = [
                    'demenageur_id' => $demenageur['id'],
                    'company_name' => $demenageur['company_name'],
                    'lead_id' => $lead_id,
                    'status' => 'sent',
                    'score' => $match['score']
                ];

            } catch (Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'demenageur_id' => $demenageur['id'],
                    'company_name' => $demenageur['company_name'],
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Crée un lead dans la base de données
     */
    private function createLead($quote_request_id, $demenageur_id, $match) {
        // Récupérer les détails du devis
        $stmt = $this->db->prepare("SELECT * FROM quote_requests WHERE id = ?");
        $stmt->execute([$quote_request_id]);
        $quote_request = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculer le coût du lead selon le plan
        $lead_cost = $this->calculateLeadCost($demenageur_id, $quote_request);

        // Date d'expiration (7 jours)
        $expires_at = date('Y-m-d H:i:s', strtotime('+7 days'));

        $sql = "
            INSERT INTO demenageur_leads
            (quote_request_id, demenageur_id, status, lead_cost, lead_details, expires_at, sent_at)
            VALUES (?, ?, 'sent', ?, ?, ?, NOW())
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $quote_request_id,
            $demenageur_id,
            $lead_cost,
            json_encode([
                'match_score' => $match['score'],
                'match_details' => $match['match_details'],
                'quote_data' => $quote_request
            ]),
            $expires_at
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Calcule le coût du lead pour le déménageur
     */
    private function calculateLeadCost($demenageur_id, $quote_request) {
        // Le coût dépend du plan et du volume du déménagement
        $stmt = $this->db->prepare("
            SELECT sp.price_per_extra_lead
            FROM demenageur_subscriptions ds
            JOIN subscription_plans sp ON ds.plan_id = sp.id
            WHERE ds.demenageur_id = ? AND ds.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$demenageur_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $base_cost = floatval($result['price_per_extra_lead'] ?? 10.00);

        // Majoration selon le volume (gros déménagements = leads plus chers)
        $volume = floatval($quote_request['total_volume'] ?? 0);
        if ($volume > 50) {
            $base_cost *= 1.5; // +50% pour gros volumes
        } elseif ($volume > 30) {
            $base_cost *= 1.25; // +25% pour volumes moyens
        }

        return round($base_cost, 2);
    }

    /**
     * Incrémente le compteur de leads utilisés ce mois
     */
    private function incrementLeadsCounter($demenageur_id) {
        $sql = "
            UPDATE demenageur_subscriptions
            SET leads_used_this_month = leads_used_this_month + 1
            WHERE demenageur_id = ? AND status = 'active'
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$demenageur_id]);
    }

    /**
     * Envoie l'email de notification au déménageur
     */
    private function sendLeadNotificationEmail($demenageur, $lead_id, $quote_request_id) {
        // Template sera créé dans emails/demenageur-nouveau-lead.php
        $to = $demenageur['email'];
        $subject = "🎯 Nouveau lead déménagement - Répondez vite !";

        // URL pour accéder au lead dans le dashboard
        $lead_url = SITE_URL . "/demenageur/lead-details.php?id=" . $lead_id;

        $message = "
            <h2>Nouveau Lead Déménagement !</h2>
            <p>Bonjour {$demenageur['contact_name']},</p>
            <p>Une nouvelle demande de devis correspond à votre zone de couverture :</p>
            <p><a href='{$lead_url}' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                Voir le lead et répondre
            </a></p>
            <p><small>Score de pertinence : {$match['score']}/100</small></p>
        ";

        // Envoi via fonction helper (à implémenter)
        return send_email($to, $subject, $message);
    }
}
