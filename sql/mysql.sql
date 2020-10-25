CREATE TABLE pmid_id (
    id        INT(30)  NOT NULL AUTO_INCREMENT,
    PMID      INT(30),
    Journal   INT(30)  NOT NULL,
    Year      CHAR(30) NOT NULL,
    Volume    CHAR(30),
    Page      CHAR(30),
    Author    TEXT     NOT NULL,
    Title     TEXT,
    Abstract  TEXT,
    R_usr     CHAR(30),
    R_date    DATE     NOT NULL DEFAULT '0000-00-00',
    F_usr     TEXT,
    F_num     INT(30),
    Title_JP  TEXT,
    Author_JP TEXT,
    Custom_t1 TEXT,
    Custom_t2 TEXT,
    Custom_t3 TEXT,
    Custom_i1 INT(30),
    Custom_i2 INT(30),
    Custom_i3 INT(30),
    PRIMARY KEY (id)
);

CREATE TABLE pmid_journal (
    id         INT(30) NOT NULL AUTO_INCREMENT,
    Journal    TEXT    NOT NULL,
    Journal_JP TEXT,
    URL        TEXT,
    Book       INT(2) DEFAULT '0',
    Editor     TEXT,
    Publisher  CHAR(30),
    Custom1    TEXT,
    Custom2    TEXT,
    PRIMARY KEY (id)
);

CREATE TABLE pmid_author (
    id      INT(30)   NOT NULL AUTO_INCREMENT,
    Author  CHAR(100) NOT NULL,
    Custom1 TEXT,
    Custom2 TEXT,
    PRIMARY KEY (id)
);

CREATE TABLE pmid_favorite_dir (
    id         INT(30)  NOT NULL AUTO_INCREMENT,
    Usr        CHAR(30) NOT NULL,
    Name       CHAR(30) NOT NULL,
    Pass       TEXT,
    Public_flg INT(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
);

CREATE TABLE pmid_favorite_data (
    id      INT(30)  NOT NULL AUTO_INCREMENT,
    data_id INT(30)  NOT NULL,
    dir_id  INT(30),
    Usr     CHAR(30) NOT NULL,
    Comment TEXT,
    PRIMARY KEY (id)
);

CREATE TABLE pmid_template (
    id       INT(30)  NOT NULL AUTO_INCREMENT,
    name     CHAR(30) NOT NULL,
    template TEXT,
    PRIMARY KEY (id)
);

CREATE TABLE pmid_shortcut (
    id     INT(30)  NOT NULL AUTO_INCREMENT,
    Usr    CHAR(30) NOT NULL,
    Name   CHAR(30) NOT NULL,
    Target TEXT     NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE pmid_memo (
    id         INT(30)  NOT NULL AUTO_INCREMENT,
    data_id    INT(30)  NOT NULL,
    R_usr      CHAR(30) NOT NULL,
    R_date     DATE     NOT NULL DEFAULT '0000-00-00',
    Comment    TEXT,
    Public_flg INT(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
);

CREATE TABLE pmid_tmp (
    tmp1 TEXT,
    tmp2 TEXT,
    tmp3 TEXT,
    tmp4 TEXT
);
