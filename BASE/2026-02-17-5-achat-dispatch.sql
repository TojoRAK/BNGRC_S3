-- Make id_don nullable (achat-dispatches have no don)
ALTER TABLE dispatch MODIFY COLUMN id_don INT NULL;

-- Add id_achat column (don-dispatches will leave this NULL)
ALTER TABLE dispatch 
    ADD COLUMN id_achat INT NULL,
    ADD CONSTRAINT fk_dispatch_achat 
        FOREIGN KEY (id_achat) REFERENCES achat(id_achat)
        ON UPDATE CASCADE ON DELETE RESTRICT;

-- Enforce that exactly one source is always set
ALTER TABLE dispatch 
    ADD CONSTRAINT chk_dispatch_source 
        CHECK (
            (id_don IS NOT NULL AND id_achat IS NULL) OR
            (id_don IS NULL AND id_achat IS NOT NULL)
        );