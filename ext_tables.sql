#
# Table structure for table 'tx_pxaproductmanager_domain_model_product'
#
CREATE TABLE tx_pxaproductmanager_domain_model_product
(
    name              varchar(255)     DEFAULT ''     NOT NULL,
    sku               varchar(55)      DEFAULT ''     NOT NULL,
    price             double(11, 2)    DEFAULT '0.00' NOT NULL,
    tax_rate          double(11, 2)    DEFAULT '0.00' NOT NULL,
    teaser            text,
    description       text,
    usp               text,

    related_products  int(11) unsigned DEFAULT '0'    NOT NULL,
    sub_products      int(11) unsigned DEFAULT '0'    NOT NULL,
    accessories       int(11) unsigned DEFAULT '0'    NOT NULL,
    images            int(11) unsigned DEFAULT '0'    NOT NULL,
    attribute_files   int(11) unsigned DEFAULT '0'    NOT NULL,
    links             int(11) unsigned DEFAULT '0'    NOT NULL,
    fal_links         int(11) unsigned DEFAULT '0'    NOT NULL,
    assets            int(11) unsigned DEFAULT '0'    NOT NULL,
    attributes_files  int(11) unsigned DEFAULT '0'    NOT NULL,
    attributes_sets   int(11) unsigned DEFAULT '0'    NOT NULL,
    attributes_values int(11) unsigned DEFAULT '0'    NOT NULL,

    alternative_title varchar(255)     DEFAULT ''     NOT NULL,
    keywords          text,
    meta_description  text,
    slug              varchar(2048)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attributeset'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attributeset
(
    name       varchar(255)     DEFAULT ''  NOT NULL,
    attributes int(11) unsigned DEFAULT '0' NOT NULL,
    categories int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attribute'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attribute
(
    name                      varchar(255)         DEFAULT ''  NOT NULL,
    label                     varchar(255)         DEFAULT ''  NOT NULL,
    type                      int(11)              DEFAULT '0' NOT NULL,
    required                  smallint(5) unsigned DEFAULT '0' NOT NULL,
    show_in_attribute_listing smallint(5) unsigned DEFAULT '0' NOT NULL,
    show_in_compare           smallint(5) unsigned DEFAULT '0' NOT NULL,
    identifier                varchar(255)         DEFAULT ''  NOT NULL,
    default_value             varchar(255)         DEFAULT ''  NOT NULL,
    options                   int(11) unsigned     DEFAULT '0' NOT NULL,
    label_checked             varchar(255)         DEFAULT ''  NOT NULL,
    label_unchecked           varchar(255)         DEFAULT ''  NOT NULL,
    icon                      int(11) unsigned     DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attributevalue'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attributevalue
(
    value     text,

    product   int(11) unsigned DEFAULT '0' NOT NULL,
    attribute int(11) unsigned DEFAULT '0' NOT NULL,

    KEY product(product),
    KEY attribute(attribute),
    KEY attribute_value(value(40))
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_option'
#
CREATE TABLE tx_pxaproductmanager_domain_model_option
(
    attribute int(11) unsigned DEFAULT '0' NOT NULL,
    value     varchar(255)     DEFAULT ''  NOT NULL
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_link'
#
CREATE TABLE tx_pxaproductmanager_domain_model_link
(
    product     int(11) unsigned DEFAULT '0' NOT NULL,

    name        varchar(255)     DEFAULT ''  NOT NULL,
    link        varchar(255)     DEFAULT ''  NOT NULL,
    description varchar(255)     DEFAULT ''  NOT NULL
);

#
# Table structure for table 'tx_pxaproductmanager_product_product_mm'
#
CREATE TABLE tx_pxaproductmanager_product_product_mm
(
    uid_local       int(11)      DEFAULT '0' NOT NULL,
    uid_foreign     int(11)      DEFAULT '0' NOT NULL,
    tablenames      varchar(255) DEFAULT ''  NOT NULL,
    fieldname       varchar(255) DEFAULT ''  NOT NULL,
    sorting         int(11)      DEFAULT '0' NOT NULL,
    sorting_foreign int(11)      DEFAULT '0' NOT NULL,

    KEY uid_local_foreign (uid_local, uid_foreign),
    KEY uid_foreign_tablefield (uid_foreign, tablenames(40), fieldname(3), sorting_foreign)
);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category
(
    pxapm_image                int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_alternative_title    varchar(255)         DEFAULT ''     NOT NULL,
    pxapm_keywords             text,
    pxapm_meta_description     text,
    pxapm_subcategories        int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_products             int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_attributes_sets      int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_description          text,
    pxapm_banner_image         int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_tax_rate             decimal(11, 2)       DEFAULT '0.00' NOT NULL,
    pxapm_slug                 varchar(2048),
    pxapm_content_page         int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_content_colpos       int(11) unsigned     DEFAULT '0'    NOT NULL,
    pxapm_hidden_in_navigation smallint(5) unsigned DEFAULT '0'    NOT NULL,
    pxapm_hide_products        smallint(5) unsigned DEFAULT '0'    NOT NULL,
    pxapm_hide_subcategories   smallint(5) unsigned DEFAULT '0'    NOT NULL
);

#
# Add type to file reference
#
CREATE TABLE sys_file_reference
(
    pxapm_type    int(11)          DEFAULT '0' NOT NULL,
    pxa_attribute int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pxaproductmanager_attributeset_record_mm'
#
CREATE TABLE tx_pxaproductmanager_attributeset_record_mm
(
    uid_local       int(11)      DEFAULT '0' NOT NULL,
    uid_foreign     int(11)      DEFAULT '0' NOT NULL,
    tablenames      varchar(255) DEFAULT ''  NOT NULL,
    fieldname       varchar(255) DEFAULT ''  NOT NULL,
    sorting         int(11)      DEFAULT '0' NOT NULL,
    sorting_foreign int(11)      DEFAULT '0' NOT NULL,

    KEY uid_local_foreign (uid_local, uid_foreign),
    KEY uid_foreign_tablefield (uid_foreign, tablenames(40), fieldname(3), sorting_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_filter'
#
CREATE TABLE tx_pxaproductmanager_domain_model_filter
(
    type        int(11)          DEFAULT '0' NOT NULL,
    name        varchar(255)     DEFAULT ''  NOT NULL,
    label       varchar(255)     DEFAULT ''  NOT NULL,
    category    int(11) unsigned DEFAULT '0' NOT NULL,
    attribute   int(11) unsigned DEFAULT '0' NOT NULL,
    conjunction varchar(10)      DEFAULT ''  NOT NULL,
);
