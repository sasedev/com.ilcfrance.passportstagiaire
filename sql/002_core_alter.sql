ALTER TABLE "ilcfrance_trainee_records" ADD COLUMN "hist_id"                                                           UUID NULL;
ALTER TABLE "ilcfrance_trainee_records" ADD CONSTRAINT "fk_ilcfrance_trainee_records_hist" FOREIGN KEY ("hist_id") REFERENCES "ilcfrance_trainee_historicals" ("id") ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE "ilcfrance_trainee_record_documents" ADD COLUMN "fileemls"                                                           INT8 NOT NULL DEFAULT 0;
