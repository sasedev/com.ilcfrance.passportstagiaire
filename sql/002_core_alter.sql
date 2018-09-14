ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "hist_id"                                                           UUID NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD CONSTRAINT "fk_ilcfrance_trainee_records_hist" FOREIGN KEY ("hist_id") REFERENCES "ilcfrance_trainee_historicals" ("id") ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE "ilcfrance_trainee_record_documents" ADD COLUMN "fileemls"                                                           INT8 NOT NULL DEFAULT 0;


ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "record_type"                                                       INT4 NOT NULL DEFAULT 1;
ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "correctionvocabulairy"                                             TEXT NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "correctionstructure"                                               TEXT NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "correctionprononciation"                                           TEXT NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "mailcomments"                                                      TEXT NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "fileemls"                                                          INT8 NOT NULL DEFAULT 0;

ALTER TABLE "ilcfrance_homeworks" ADD COLUMN "level_id"                                                           UUID NULL;
ALTER TABLE "ilcfrance_homeworks" ADD CONSTRAINT "fk_ilcfrance_homeworks_level" FOREIGN KEY ("level_id") REFERENCES "ilcfrance_levels" ("id") ON UPDATE CASCADE ON DELETE SET NULL;

