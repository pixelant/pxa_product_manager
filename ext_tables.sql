#
# Table structure for table 'tx_pxaproductmanager_domain_model_product'
#
CREATE TABLE tx_pxaproductmanager_domain_model_product (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  sku varchar(255) DEFAULT '' NOT NULL,
  price double(11,2) DEFAULT '0.00' NOT NULL,
  tax_rate decimal(5,2) DEFAULT '0.00' NOT NULL,
  description text,
  disable_single_view tinyint(1) unsigned DEFAULT '0' NOT NULL,
  attribute_values  varchar(255) DEFAULT '' NOT NULL,
  related_products int(11) unsigned DEFAULT '0' NOT NULL,
  images int(11) unsigned DEFAULT '0',
  attribute_files int(11) unsigned DEFAULT '0',
  links int(11) unsigned DEFAULT '0' NOT NULL,
  sub_products int(11) unsigned DEFAULT '0' NOT NULL,
  path_segment tinytext,
  alternative_title tinytext,
  keywords text,
  meta_description text,
  fal_links int(11) unsigned DEFAULT '0',
  serialized_attributes_values blob,
  attributes_description text,
  assets int(11) unsigned DEFAULT '0' NOT NULL,
  accessories int(11) unsigned DEFAULT '0' NOT NULL,
  additional_information text,
  teaser text,
  launched int(11) unsigned DEFAULT '0' NOT NULL,
  discontinued int(11) unsigned DEFAULT '0' NOT NULL,
  usp text,
  custom_sorting int(11) DEFAULT '0' NOT NULL,
  slug varchar(2048),

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,

  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attributeset'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attributeset (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  attributes int(11) unsigned DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  sorting int(11) DEFAULT '0' NOT NULL,
  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attribute'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attribute (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  label varchar(255) DEFAULT '' NOT NULL,
  type int(11) DEFAULT '0' NOT NULL,
  required tinyint(1) unsigned DEFAULT '0' NOT NULL,
  show_in_attribute_listing tinyint(1) unsigned DEFAULT '0' NOT NULL,
  show_in_compare tinyint(1) unsigned DEFAULT '0' NOT NULL,
  identifier varchar(255) DEFAULT '' NOT NULL,
  default_value varchar(255) DEFAULT '' NOT NULL,
  options int(11) unsigned DEFAULT '0' NOT NULL,
  label_checked varchar(255) DEFAULT '' NOT NULL,
  label_unchecked varchar(255) DEFAULT '' NOT NULL,
  icon int(11) unsigned DEFAULT '0',

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_attributevalue'
#
CREATE TABLE tx_pxaproductmanager_domain_model_attributevalue (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  product int(11) unsigned DEFAULT '0' NOT NULL,

  value text,
  attribute int(11) unsigned DEFAULT '0',

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,

  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid),
  KEY productattribute (product,attribute)

);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_option'
#
CREATE TABLE tx_pxaproductmanager_domain_model_option (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  attribute int(11) unsigned DEFAULT '0' NOT NULL,
  order_field int(11) unsigned DEFAULT '0' NOT NULL,
  value varchar(255) DEFAULT '' NOT NULL,

  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,

  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_link'
#
CREATE TABLE tx_pxaproductmanager_domain_model_link (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  product int(11) unsigned DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  link varchar(255) DEFAULT '' NOT NULL,
  description tinytext,

  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,

  t3_origuid int(11) DEFAULT '0' NOT NULL,
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_product_product_mm'
#
CREATE TABLE tx_pxaproductmanager_product_product_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_product_subproducts_product_mm'
#
CREATE TABLE tx_pxaproductmanager_product_subproducts_product_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (
  pxapm_image int(11) unsigned DEFAULT '0',
  path_segment tinytext,
  alternative_title tinytext,
  keywords text,
  meta_description text,
  pxapm_subcategories int(11) unsigned DEFAULT '0',
  pxapm_attributes_sets int(11) unsigned DEFAULT '0' NOT NULL,
  pxapm_description text,
  pxapm_banner_image int(11) unsigned DEFAULT '0',
  pxapm_card_view_template varchar(255) DEFAULT '' NOT NULL,
  pxapm_single_view_template varchar(255) DEFAULT '' NOT NULL,
  pxapm_tax_rate decimal(5,2) DEFAULT '0.00' NOT NULL,
  pxapm_slug varchar(2048)
);

#
# Add show in preview to file reference
#
CREATE TABLE sys_file_reference (
  pxapm_use_in_listing tinyint(4) DEFAULT '0' NOT NULL,
  pxapm_main_image tinyint(4) DEFAULT '0' NOT NULL,
  pxa_attribute int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pxaproductmanager_attributeset_attribute_mm'
#
CREATE TABLE tx_pxaproductmanager_attributeset_attribute_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_product_category_mm'
#
CREATE TABLE tx_pxaproductmanager_category_attributeset_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_filter'
#
CREATE TABLE tx_pxaproductmanager_domain_model_filter (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  type int(11) DEFAULT '0' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  label varchar(255) DEFAULT '' NOT NULL,
  parent_category int(11) unsigned DEFAULT '0',
  attribute int(11) unsigned DEFAULT '0',
  inverse_conjunction tinyint(4) unsigned DEFAULT '0' NOT NULL,

  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(255) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage int(11) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
  t3ver_move_id int(11) DEFAULT '0' NOT NULL,

  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid),
  KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_pxaproductmanager_product_accessories_product_mm'
#
CREATE TABLE tx_pxaproductmanager_product_accessories_product_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_order'
#
CREATE TABLE tx_pxaproductmanager_domain_model_order (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	products int(11) unsigned DEFAULT '0' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	complete tinyint(4) unsigned DEFAULT '0' NOT NULL,
  	serialized_order_fields blob,
  	serialized_products_quantity blob,
  	external_id varchar(255) DEFAULT '' NOT NULL,
  	checkout_type varchar(255) DEFAULT 'default' NOT NULL,
  	coupons int(11) unsigned DEFAULT '0' NOT NULL,
  	price_at_checkout double(11,2) DEFAULT '0.00' NOT NULL,
  	tax_at_checkout double(11,2) DEFAULT '0.00' NOT NULL,
    subscription int(11) unsigned DEFAULT '0' NOT NULL,
    state_hash varchar(32) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_pxaproductmanager_order_product_mm'
#
CREATE TABLE tx_pxaproductmanager_order_product_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_order_coupon_mm'
#
CREATE TABLE tx_pxaproductmanager_order_coupon_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_orderconfiguration'
#
CREATE TABLE tx_pxaproductmanager_domain_model_orderconfiguration (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(55) DEFAULT '' NOT NULL,
	enabled_email_to_user tinyint(4) unsigned DEFAULT '0' NOT NULL,
  enabled_replace_with_fe_user_fields tinyint(4) unsigned DEFAULT '0' NOT NULL,
  form_fields  int(11) unsigned DEFAULT '0' NOT NULL,
  admin_emails text,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_orderformfield'
#
CREATE TABLE tx_pxaproductmanager_domain_model_orderformfield (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

  type int(11) DEFAULT '0' NOT NULL,
  order_configuration int(11) DEFAULT '0' NOT NULL,
	name varchar(55) DEFAULT '' NOT NULL,
  label varchar(255) DEFAULT '' NOT NULL,
  placeholder varchar(255) DEFAULT '' NOT NULL,
  options int(11) unsigned DEFAULT '0' NOT NULL,
  validation_rules varchar(255) DEFAULT '' NOT NULL,
  user_email_field tinyint(4) unsigned DEFAULT '0' NOT NULL,
  additional_text text,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_cupon'
#
CREATE TABLE tx_pxaproductmanager_domain_model_coupon (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	code varchar(255) DEFAULT '' NOT NULL,
	type tinyint(4) unsigned DEFAULT '0' NOT NULL,
	value double(11,2) DEFAULT '0.00' NOT NULL,
	usage_limit int(11) unsigned DEFAULT '0' NOT NULL,
	cost_limit double(11,2) DEFAULT '0.00' NOT NULL,
	usage_count int(11) unsigned DEFAULT '0' NOT NULL,
	total_cost double(11,2) DEFAULT '0.00' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY couponcode (code),
	KEY parent (pid)
);

#
# Table structure for table 'tx_pxaproductmanager_domain_model_subscription'
#
CREATE TABLE tx_pxaproductmanager_domain_model_subscription (
   uid int(11) NOT NULL auto_increment,
   pid int(11) DEFAULT '0' NOT NULL,

   renew_date int(11) unsigned DEFAULT '0' NOT NULL,
   next_try int(11) unsigned DEFAULT '0' NOT NULL,
   status smallint(5) unsigned DEFAULT '0' NOT NULL,
   last_renew_status varchar(255) DEFAULT '' NOT NULL,
   attempts_left int(11) unsigned DEFAULT '0' NOT NULL,
   orders int(11) unsigned DEFAULT '0' NOT NULL,
   serialized_products_quantity blob,
   subscription_period smallint(5) unsigned DEFAULT '0' NOT NULL,

   tstamp int(11) unsigned DEFAULT '0' NOT NULL,
   crdate int(11) unsigned DEFAULT '0' NOT NULL,
   cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
   deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
   hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
   starttime int(11) unsigned DEFAULT '0' NOT NULL,
   endtime int(11) unsigned DEFAULT '0' NOT NULL,

   PRIMARY KEY (uid),
   KEY parent (pid)
);
