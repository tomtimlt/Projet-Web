<?php
/**
 * Configuration des rôles et permissions
 */
return [
    'roles' => [
        'admin' => 'Administrateur',
        'pilote' => 'Pilote',
        'etudiant' => 'Étudiant'
    ],
    'permissions' => [
        'SFx1' => [
            'name' => 'Authentifier',
            'category' => 'Gestion d\'accès',
            'roles' => ['admin', 'pilote', 'etudiant']
        ],
        'SFx2' => [
            'name' => 'Rechercher une entreprise',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote', 'etudiant']
        ],
        'SFx3' => [
            'name' => 'Créer une entreprise',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote']
        ],
        'SFx4' => [
            'name' => 'Modifier une entreprise',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote']
        ],
        'SFx5' => [
            'name' => 'Evaluer une entreprise',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote', 'etudiant']
        ],
        'SFx6' => [
            'name' => 'Supprimer une entreprise',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote']
        ],
        'SFx7' => [
            'name' => 'Consulter les stats des entreprises',
            'category' => 'Gestion des entreprises',
            'roles' => ['admin', 'pilote']
        ],
        'SFx8' => [
            'name' => 'Rechercher une offre',
            'category' => 'Gestion des offres de stages',
            'roles' => ['admin', 'pilote', 'etudiant']
        ],
        'SFx9' => [
            'name' => 'Créer une offre',
            'category' => 'Gestion des offres de stages',
            'roles' => ['admin', 'pilote']
        ],
        'SFx10' => [
            'name' => 'Modifier une offre',
            'category' => 'Gestion des offres de stages',
            'roles' => ['admin', 'pilote']
        ],
        'SFx11' => [
            'name' => 'Supprimer une offre',
            'category' => 'Gestion des offres de stages',
            'roles' => ['admin', 'pilote']
        ],
        'SFx12' => [
            'name' => 'Consulter les stats des offres',
            'category' => 'Gestion des offres de stages',
            'roles' => ['admin', 'pilote']
        ],
        'SFx13' => [
            'name' => 'Rechercher un compte pilote',
            'category' => 'Gestion des Pilotes',
            'roles' => ['admin']
        ],
        'SFx14' => [
            'name' => 'Créer un compte pilote',
            'category' => 'Gestion des Pilotes',
            'roles' => ['admin']
        ],
        'SFx15' => [
            'name' => 'Modifier un compte pilote',
            'category' => 'Gestion des Pilotes',
            'roles' => ['admin']
        ],
        'SFx16' => [
            'name' => 'Supprimer un compte pilote',
            'category' => 'Gestion des Pilotes',
            'roles' => ['admin']
        ],
        'SFx17' => [
            'name' => 'Rechercher un compte étudiant',
            'category' => 'Gestion des étudiants',
            'roles' => ['admin', 'pilote']
        ],
        'SFx18' => [
            'name' => 'Créer un compte étudiant',
            'category' => 'Gestion des étudiants',
            'roles' => ['admin', 'pilote']
        ],
        'SFx19' => [
            'name' => 'Modifier un compte étudiant',
            'category' => 'Gestion des étudiants',
            'roles' => ['admin', 'pilote']
        ],
        'SFx20' => [
            'name' => 'Supprimer un compte étudiant',
            'category' => 'Gestion des étudiants',
            'roles' => ['admin', 'pilote']
        ],
        'SFx21' => [
            'name' => 'Consulter les stats des étudiants',
            'category' => 'Gestion des étudiants',
            'roles' => ['admin', 'pilote']
        ],
        'SFx22' => [
            'name' => 'Ajouter une offre à la wish-list',
            'category' => 'Gestion des candidatures',
            'roles' => ['etudiant']
        ],
        'SFx23' => [
            'name' => 'Retirer une offre à la wish-list',
            'category' => 'Gestion des candidatures',
            'roles' => ['etudiant']
        ],
        'SFx24' => [
            'name' => 'Postuler à une offre',
            'category' => 'Gestion des candidatures',
            'roles' => ['etudiant']
        ],
    ]
];
