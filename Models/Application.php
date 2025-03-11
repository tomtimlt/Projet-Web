<?php

namespace Models;

/**
 * Modèle pour la gestion des candidatures aux offres
 */
class Application
{
    private $db;

    /**
     * Constructeur
     */
    public function __construct()
    {
        // Utiliser le singleton Database au lieu de créer une nouvelle connexion
        $database = \Database\Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Créer une nouvelle candidature
     *
     * @param int $offerId ID de l'offre
     * @param int $studentId ID de l'étudiant
     * @param string $motivation Lettre de motivation
     * @param string $cvFile Chemin du fichier CV
     * @return int|bool ID de la candidature ou false en cas d'échec
     */
    public function createApplication($offerId, $studentId, $motivation, $cvFile = null)
    {
        try {
            // Vérifier si l'étudiant a déjà postulé à cette offre
            $check = $this->db->prepare("
                SELECT id FROM applications 
                WHERE student_id = :student_id AND offer_id = :offer_id
            ");
            $check->execute([
                ':student_id' => $studentId,
                ':offer_id' => $offerId
            ]);

            if ($check->rowCount() > 0) {
                return false; // L'étudiant a déjà postulé à cette offre
            }

            // Créer la candidature
            $stmt = $this->db->prepare("
                INSERT INTO applications (offer_id, student_id, motivation, cv_file, status, created_at, updated_at) 
                VALUES (:offer_id, :student_id, :motivation, :cv_file, 'pending', NOW(), NOW())
            ");
            $result = $stmt->execute([
                ':offer_id' => $offerId,
                ':student_id' => $studentId,
                ':motivation' => $motivation,
                ':cv_file' => $cvFile
            ]);

            if ($result) {
                // Retourner l'ID de la nouvelle candidature
                return $this->db->lastInsertId();
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création de la candidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour le statut d'une candidature
     *
     * @param int $applicationId ID de la candidature
     * @param string $status Nouveau statut (pending, accepted, rejected, withdrawn)
     * @return bool Succès de l'opération
     */
    public function updateStatus($applicationId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE applications 
                SET status = :status, updated_at = NOW() 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':id' => $applicationId,
                ':status' => $status
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut de la candidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir une candidature par son ID
     *
     * @param int $applicationId ID de la candidature
     * @return array|bool Données de la candidature ou false si non trouvée
     */
    public function getApplicationById($applicationId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, o.title as offer_title, c.name as company_name 
                FROM applications a
                JOIN offers o ON a.offer_id = o.id
                JOIN companies c ON o.company_id = c.id
                WHERE a.id = :id
            ");
            $stmt->execute([':id' => $applicationId]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la candidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les candidatures d'un étudiant
     *
     * @param int $studentId ID de l'étudiant
     * @return array Liste des candidatures
     */
    public function getApplicationsByStudentId($studentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, o.title as offer_title, c.name as company_name, 
                       o.location, o.start_date, o.end_date
                FROM applications a
                JOIN offers o ON a.offer_id = o.id
                JOIN companies c ON o.company_id = c.id
                WHERE a.student_id = :student_id
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([':student_id' => $studentId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des candidatures: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si un étudiant a déjà postulé à une offre
     *
     * @param int $studentId ID de l'étudiant
     * @param int $offerId ID de l'offre
     * @return bool True si l'étudiant a déjà postulé, false sinon
     */
    public function hasApplied($studentId, $offerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM applications 
                WHERE student_id = :student_id AND offer_id = :offer_id
            ");
            $stmt->execute([
                ':student_id' => $studentId,
                ':offer_id' => $offerId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la vérification de la candidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une candidature
     *
     * @param int $applicationId ID de la candidature
     * @param int $studentId ID de l'étudiant (pour vérification)
     * @return bool Succès de l'opération
     */
    public function deleteApplication($applicationId, $studentId)
    {
        try {
            // Vérifier que la candidature appartient à l'étudiant
            $check = $this->db->prepare("
                SELECT id FROM applications 
                WHERE id = :id AND student_id = :student_id
            ");
            $check->execute([
                ':id' => $applicationId,
                ':student_id' => $studentId
            ]);

            if ($check->rowCount() === 0) {
                return false; // La candidature n'appartient pas à cet étudiant
            }

            $stmt = $this->db->prepare("
                DELETE FROM applications 
                WHERE id = :id
            ");
            return $stmt->execute([':id' => $applicationId]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression de la candidature: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre de candidatures par statut pour un étudiant
     *
     * @param int $studentId ID de l'étudiant
     * @return array Statistiques des candidatures par statut
     */
    public function getApplicationStatsByStudentId($studentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT status, COUNT(*) as count
                FROM applications
                WHERE student_id = :student_id
                GROUP BY status
            ");
            $stmt->execute([':student_id' => $studentId]);
            
            $results = $stmt->fetchAll();
            $stats = [
                'pending' => 0,
                'accepted' => 0,
                'rejected' => 0,
                'withdrawn' => 0,
                'total' => 0
            ];

            foreach ($results as $row) {
                $stats[$row['status']] = (int)$row['count'];
                $stats['total'] += (int)$row['count'];
            }

            return $stats;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return [
                'pending' => 0,
                'accepted' => 0,
                'rejected' => 0,
                'withdrawn' => 0,
                'total' => 0
            ];
        }
    }

    /**
     * Récupérer toutes les candidatures (pour administrateurs)
     * 
     * @return array Liste de toutes les candidatures
     */
    public function getAllApplications()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, o.title as offer_title, o.company_id, c.name as company_name,
                u.firstname, u.lastname, u.email
                FROM applications a
                INNER JOIN offers o ON a.offer_id = o.id
                INNER JOIN companies c ON o.company_id = c.id
                INNER JOIN users u ON a.student_id = u.id
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération de toutes les candidatures: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les candidatures pour une offre spécifique
     * 
     * @param int $offerId ID de l'offre
     * @return array Liste des candidatures pour cette offre
     */
    public function getApplicationsByOfferId($offerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.firstname, u.lastname, u.email
                FROM applications a
                INNER JOIN users u ON a.student_id = u.id
                WHERE a.offer_id = :offer_id
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([':offer_id' => $offerId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des candidatures pour une offre: " . $e->getMessage());
            return [];
        }
    }
}
