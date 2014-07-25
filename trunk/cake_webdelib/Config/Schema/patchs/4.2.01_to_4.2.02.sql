--
-- Début du patch : début de la transaction
--
BEGIN;

-- modelvariables
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (300, 'nom_president', 'Nom du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (301, 'prenom_president', 'Prénom du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (302, 'salutation_president', 'Civilité du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (303, 'titre_president', 'Titre du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (304, 'date_naissance_president', 'Date de naissance du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (305, 'adresse1_president', 'Adresse 1 du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (306, 'adresse2_president', 'Adresse 2 du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (307, 'cp_president', 'Code postal du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (308, 'ville_president', 'Ville de résidence du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (309, 'email_president', 'Adresse mail du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (310, 'telfixe_president', 'Numéro de téléphone fixe du président de séance', now(), now());
INSERT INTO public.modelvariables (id, name, description, created, modified) VALUES (311, 'note_president', 'Note rédigée sur le président de séance', now(), now());

-- modelvalidations
INSERT INTO public.modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1500, 51, 1, 5, 0, 0, true);
INSERT INTO public.modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1501, 52, 1, 5, 0, 0, true);
INSERT INTO public.modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1502, 53, 1, 5, 0, 0, true);
INSERT INTO public.modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1503, 54, 1, 5, 0, 0, true);

-- public.modelvalidations::id = 2000
ALTER SEQUENCE modelvalidations_id_seq RESTART WITH 2000;

-- règles *_president
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 3, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 2, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 2, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 2, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 2, 9, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 3, 3, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 3, 6, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 3, 7, 0, 0, true);
INSERT INTO public.modelvalidations (modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 3, 9, 0, 0, true);

--
-- Fin du patch : fin de la transaction
--
COMMIT;