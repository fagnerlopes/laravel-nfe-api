CREATE TABLE estados(
        id  INT  AUTO_INCREMENT    NOT NULL  ,
        nome VARCHAR  (200)     DEFAULT NULL,
        uf VARCHAR  (2)   ,
        codigo_ibge VARCHAR  (2)   ,
        regiao CHAR  (1),
        perc_icms DOUBLE,
        created_at DATETIME,
        sync_at DATETIME  DEFAULT NULL,
        update_at DATETIME ,
        deleted_at DATETIME ,
PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE cidades(
        id  INT  AUTO_INCREMENT    NOT NULL  ,
        nome VARCHAR  (100)   NOT NULL  ,
        estado_id INT   NOT NULL  ,
        created_at DATETIME,
        sync_at DATETIME  DEFAULT NULL,
        update_at DATETIME ,
        deleted_at DATETIME ,
        codigo_ibge VARCHAR  (20)   ,
PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE cidades ADD CONSTRAINT fk_cidades_estado_id FOREIGN KEY (estado_id) REFERENCES estados(id);







