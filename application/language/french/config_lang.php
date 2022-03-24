<?php
$lang['config_info']='Informations sur la configuration du magasin'; 

$lang['config_address']='Adresse de la compagnie';
$lang['config_phone']='Téléphone de la compagnie'; 

$lang['config_fax']='Télécopieur';
$lang['config_default_tax_rate']='Pourcentage de taxe par défaut';


$lang['config_company_required']='Le champ Nom de compagnie est requis'; 

$lang['config_phone_required']='Le champ Téléphone est requis'; 
$lang['config_default_tax_rate_required']='Le champ Pourcentage taxe par défaut est requis'; 
$lang['config_default_tax_rate_number']='Le pourcentage de taxe par défaut doit être un chiffre ou un nombre'; 
$lang['config_company_website_url']='Le site web de la compagnie n\'est pas un URL valide (http://...)'; 

$lang['config_saved_unsuccessfully']='La configuration n\'a pas été enregistrée';
$lang['config_return_policy_required']='Le champ Politique de retour est requis'; 
$lang['config_print_after_sale']='Imprimer le reçu après la vente'; 


$lang['config_currency_symbol'] = 'Symbole monétaire';
$lang['config_backup_database'] = 'Sauvegarder la base de données';
$lang['config_restore_database'] = 'Restaurer la base de données';

$lang['config_number_of_items_per_page'] = 'Nombre d\'articles par page';
$lang['config_date_format'] = 'Format de date';
$lang['config_time_format'] = 'Format de l\'heure';



$lang['config_optimize_database'] = 'Optimiser la base de données';
$lang['config_database_optimize_successfully'] = 'Base de données optimisée avec succès'; 
$lang['config_payment_types'] = 'Types de paiements';
$lang['select_sql_file'] = 'sélectionnez un fichier .sql';

$lang['restore_heading'] = 'Cela vous permet de restaurer votre base de données';

$lang['type_file'] = 'sélectionner un fichier .sql de votre ordinateur';

$lang['restore'] = 'restaurer';

$lang['required_sql_file'] = 'Aucun fichier .sql n\'a été sélectionné'; 

$lang['restore_db_success'] = 'Base de données restaurée avec succès'; 

$lang['db_first_alert'] = 'Êtes-vous sûr de vouloir restaurer la base de données?'; 
$lang['db_second_alert'] = 'Les données actuelles seront perdues, voulez-vous continuer?'; 
$lang['password_error'] = 'Mot de passe incorrect';
$lang['password_required'] = 'Le champ Mot de passe est requis';
$lang['restore_database_title'] = 'Restaurer la base de données';
$lang['config_use_scale_barcode'] = "Utilisez le code barre de la balance";

$lang['config_environment'] = 'Environnement';


$lang['config_sandbox'] = 'Bac à sable'; 
$lang['config_production'] = 'Production';
$lang['disable_confirmation_sale']='Désactiver la confirmation pour conclure la vente';




$lang['config_default_payment_type'] = 'Type de paiement par défaut';
$lang['config_speed_up_note'] = 'Recommandé uniquement si vous avez plus de 10000 articles ou clients';
$lang['config_hide_signature'] = 'Masquer la signature';
$lang['config_automatically_email_receipt']='Envoyer automatiquement le reçu par courriel';
$lang['config_barcode_price_include_tax']='Inclure taxe sur les codes-barres';
$lang['config_round_cash_on_sales'] = 'Arrondir à 0,05 lors de paiements comptant';
$lang['config_prefix'] = 'Préfixe d\'identification de la vente';
$lang['config_sale_prefix_required'] = 'Le champ Préfixe d\'identification de la vente est requis';
$lang['config_customers_store_accounts'] = 'Comptes clients ';
$lang['config_change_sale_date_when_suspending'] = 'Changer la date de la vente lors de la suspension';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Changer la date de vente au moment de compléter la vente suspendue';
$lang['config_price_tiers'] = 'Niveaux de prix';
$lang['config_add_tier'] = 'Ajouter un niveau';
$lang['config_show_receipt_after_suspending_sale'] = 'Montrer le reçu après avoir suspendu la vente';
$lang['config_backup_overview'] = 'Aperçu de la sauvegarde';
$lang['config_backup_overview_desc'] = 'La sauvegarde de vos données est très importante, mais peut être difficile avec une grande quantité de données. Si vous avez beaucoup d\'images, d\'objets ou de ventes, cela peut augmenter la taille de votre base de données.';
$lang['config_backup_options'] = 'Nous offrons de nombreuses options de sauvegarde pour vous aider à décider comment procéder';
$lang['config_backup_simple_option'] = 'En cliquant sur Sauvgarder la base de données, le système va essayer de télécharger l\'ensemble de votre base dans un fichier. Si vous obtenez un écran blanc ou ne pouvez pas télécharger le fichier, essayez l\'une des autres options.';
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin est un outil populaire pour la gestion de vos bases des données. Si vous utilisez la version incluse avec l\'installateur, il est possible d\' y accéder en allant à';
$lang['config_backup_phpmyadmin_2'] = 'Le nom d\'utilisateur est root et le mot de passe est celui que vous avez utilisé lors de l\'installation initiale de PHP POS. Une fois connecté, sélectionnez votre base de données à partir du panneau sur la gauche. Ensuite, sélectionnez "export" et soumettre le formulaire.';
$lang['config_backup_control_panel'] = 'Si PHP POS à été installé sur un serveur disposant d\'un panneau de contrôle tel que cPanel, recherchez le module de sauvegarde qui vous permettra de télécharger des sauvegardes de votre base de données.';
$lang['config_backup_mysqldump'] = 'Si vous avez accès à l\'invite de commandes et mysqldump sur votre serveur, vous pouvez essayer de l\'exécuter en cliquant sur le lien ci-dessous. Sinon, vous devrez essayer d\'autres options.';
$lang['config_mysqldump_failed'] = 'La sauvegarde mysqldump a échoué. Cela pourrait être dû à une restriction du serveur ou la commande peut ne pas être disponible. S\'il vous plaît essayer une autre méthode de sauvegarde';



$lang['config_looking_for_location_settings'] = 'Vous cherchez d\'autres options de configuration? Allez à';
$lang['config_module'] = 'Module';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Calculer automatiquement le prix moyen de la reception';
$lang['config_averaging_method'] = 'Méthode moyenne';
$lang['config_historical_average'] = 'Historique de la moyenne';
$lang['config_moving_average'] = 'Moyenne des deplacements';

$lang['config_hide_dashboard_statistics'] = 'Cachez les statistiques du tableau de bord';
$lang['config_hide_store_account_payments_in_reports'] = 'Masquer les paiements du compte magasin dans les rapports';
$lang['config_id_to_show_on_sale_interface'] = 'Afficher l\'ID d\'article sur l\'interface de vente';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Focus automatique sur l\'article lorsque vous utilisez l\'interface ventes ou réception';
$lang['config_automatically_show_comments_on_receipt'] = 'Afficher automatiquement les commentaires sur le reçu';
$lang['config_hide_customer_recent_sales'] = 'Masquer les ventes récentes des clients';
$lang['config_spreadsheet_format'] = 'Format du tableau';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Désactiver la détection des carte cadeau';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Désactiver la soustraction du montant de la carte cadeau lors de la vente';
$lang['config_always_show_item_grid'] = 'Toujours afficher la grille des articles';
$lang['config_legacy_detailed_report_export'] = 'Export un rapport détaillé Excel';
$lang['config_print_after_receiving'] = 'Imprimer un reçu après une réception';
$lang['config_company_info'] = 'Informations sur la société';


$lang['config_suspended_sales_layaways_info'] = 'Ventes/mises de côté suspendues';
$lang['config_application_settings_info'] = 'Paramètres de l\'application';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Cachez le code à barres sur les reçus';
$lang['config_round_tier_prices_to_2_decimals'] = 'Arrondir les niveaux de prix à 2 décimales';
$lang['config_group_all_taxes_on_receipt'] = 'Regrouper toutes les taxes sur le reçu';
$lang['config_receipt_text_size'] = 'Taille du texte sur le reçu';
$lang['config_small'] = 'Petit';
$lang['config_medium'] = 'Moyen';
$lang['config_large'] = 'Grand';
$lang['config_extra_large'] = 'Très grand';
$lang['config_select_sales_person_during_sale'] = 'Sélectionnez un vendeur lors de la vente';
$lang['config_default_sales_person'] = 'Vendeur par défaut';
$lang['config_require_customer_for_sale'] = 'Exiger la saisie d\'un client lors de la vente';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Masquer les paiements du compte magasin des totaux des rapports';
$lang['config_disable_sale_notifications'] = 'Désactiver la notification de vente';
$lang['config_id_to_show_on_barcode'] = 'Afficher ID sur le code à barres';
$lang['config_currency_denoms'] = 'Coupures';
$lang['config_currency_value'] = 'Valeur de la devise';
$lang['config_add_currency_denom'] = 'Ajouter les coupures';
$lang['config_enable_timeclock'] = 'Activer l\'hodoraeur';
$lang['config_change_sale_date_for_new_sale'] = 'Modification de la date de vente pour les nouvelles ventes';
$lang['config_dont_average_use_current_recv_price'] = 'Ne pas faire une moyenne, utiliser le prix actuel';
$lang['config_number_of_recent_sales'] = 'Montrer les ventes récentes pour le client';
$lang['config_hide_suspended_recv_in_reports'] = 'Cacher les réceptions suspendues dans les rapports';
$lang['config_calculate_profit_for_giftcard_when'] = 'Calculer le bénéfice d\'une carte-cadeau lors';
$lang['config_selling_giftcard'] = 'Vente de cartes-cadeaux';
$lang['config_redeeming_giftcard'] = 'Encaisser des cartes-cadeaux';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Retirer informations de contact client de la réception';
$lang['config_speed_up_search_queries'] = 'Accélérer les requêtes de recherche?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Rediriger à la vente ou à l\'écran de réception après l\'impression réception';
$lang['config_enable_sounds'] = 'Activer les sons pour les messages d\'état';
$lang['config_charge_tax_on_recv'] = 'Facturer la taxe sur les réceptions';
$lang['config_report_sort_order'] = 'Ordre de tri du rapport';
$lang['config_asc'] = 'Plus ancien en premier';
$lang['config_desc'] = 'Plus récent en premier';
$lang['config_do_not_group_same_items'] = 'Ne pas regrouper les éléments qui sont les mêmes';
$lang['config_show_item_id_on_receipt'] = 'Voir l\'ID de l\'article à la réception';
$lang['config_show_language_switcher'] = 'Afficher le commutateur de langue';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Ne autoriser la vente d\'items en rupture de stock';
$lang['config_number_of_items_in_grid'] = 'Nombre d\'articles par page dans la grille';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Modifier prix de l\'objet si 0 après l\'ajout à la vente';
$lang['config_override_receipt_title'] = 'Remplacer le titre de la réception';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Imprimer automatiquement le reçu en double pour les transactions par carte de crédit';






$lang['config_default_type_for_grid'] = 'Type par défaut pour la grille';
$lang['config_billing_is_managed_through_paypal'] = 'La facturation est gérée par <a target="_blank" href="http://paypal.com">Paypal</a>. Vous pouvez annuler votre abonnement en cliquant <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">ici</a>. <a href="http://phppointofsale.com/update_billing.php" target="_blank">Vous pouvez mettre à jour la facturation ici</a>';
$lang['config_cannot_change_language'] = 'La langue ne peut pas être enregistrée au niveau de l\'application. Toutefois, l\'administrateur par défaut peut changer la langue à l\'aide du sélecteur dans l\'en-tête du programme';
$lang['disable_quick_complete_sale'] = 'Désactiver la vente rapide complète';
$lang['config_fast_user_switching'] = 'Activer la permutation rapide d\'utilisateur';
$lang['config_require_employee_login_before_each_sale'] = 'Exiger la connexion de l\'employé avant chaque vente';
$lang['config_reset_location_when_switching_employee'] = 'Réinitialiser l\'emplacement lors de la commutation employé';
$lang['config_number_of_decimals'] = 'Nombre de décimales';
$lang['config_let_system_decide'] = 'Laissez décider le système (recommandé)';
$lang['config_thousands_separator'] = 'Séparateur de milliers';
$lang['config_enhanced_search_method'] = 'Méthode de recherche améliorée';
$lang['config_hide_store_account_balance_on_receipt'] = 'Cacher la balance du magasin à la réception';
$lang['config_decimal_point'] = 'Virgule';

$lang['config_hide_out_of_stock_grid'] = 'Cacher des articles en stock dans la grille';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Mettez en surbrillance faibles objets de l\'inventaire dans le module des articles';
$lang['config_sort'] = 'Genre';
$lang['config_enable_customer_loyalty_system'] = 'Activer le système de fidélisation de la clientèle';
$lang['config_spend_to_point_ratio'] = 'Passez montant à pointer rapport';
$lang['config_point_value'] = 'Valeur ponctuelle';
$lang['config_hide_points_on_receipt'] = 'Cacher Points à la réception';
$lang['config_show_clock_on_header'] = 'Afficher l\'horloge en tête';
$lang['config_show_clock_on_header_help_text'] = 'Ceci est visible seulement sur les écrans larges';
$lang['config_loyalty_explained_spend_amount'] = 'Entrez le montant à dépenser';
$lang['config_loyalty_explained_points_to_earn'] = 'Entrez les points à gagner';
$lang['config_simple'] = 'Simple';
$lang['config_advanced'] = 'Avancée';
$lang['config_loyalty_option'] = 'Option du programme de fidélisation';
$lang['config_number_of_sales_for_discount'] = 'Nombre de ventes pour une réduction';
$lang['config_discount_percent_earned'] = 'Pour cent de rabais obtenu en atteignant des ventes';
$lang['hide_sales_to_discount_on_receipt'] = 'Cacher les ventes à rabais à la réception';
$lang['config_hide_price_on_barcodes'] = 'Masquer prix sur les codes-barres';
$lang['config_always_use_average_cost_method'] = 'Toujours Use Global Coût moyen Prix pour le coût du prix d\'un article de vente. (NE PAS vérifier si vous savez ce que cela signifie)';

$lang['config_test_mode_help'] = 'Sales pas enregistré';
$lang['config_require_customer_for_suspended_sale'] = 'Exiger client à vendre suspendu';
$lang['config_default_new_items_to_service'] = 'Par défaut Nouveaux Articles que les éléments de service';






$lang['config_prompt_for_ccv_swipe'] = 'Demander le CCV avant de faire glisser la carte de crédit';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Compte sur le magasin Désactiver lorsque plus de limite de crédit';
$lang['config_mailing_labels_type'] = 'Mailing Labels Format';
$lang['config_phppos_session_expiration'] = 'Expiration de la session';
$lang['config_hours'] = 'Des heures';
$lang['config_never'] = 'Jamais';
$lang['config_on_browser_close'] = 'Sur Browser Fermer';
$lang['config_do_not_allow_below_cost'] = 'Ne laissez pas les articles destinés à être vendus en dessous du prix de revient';
$lang['config_store_account_statement_message'] = 'Magasin relevé de compte du message';
$lang['config_enable_margin_calculator'] = 'Activer Mark Calculator Up';
$lang['config_enable_quick_edit'] = 'Activer édition rapide sur la gestion des pages';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Afficher prix initial à la réception si marquée vers le bas';
$lang['config_cancel_account'] = 'Annuler le compte';
$lang['config_update_billing'] = 'Vous pouvez mettre à jour et annuler vos informations de facturation en cliquant sur les boutons ci-dessous:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Inclure les catégories d\'enfants lors de la recherche ou de rapports';
$lang['config_confirm_error_messages_modal'] = 'Confirmez les messages d\'erreur en utilisant des boîtes de dialogue modales';
$lang['config_remove_commission_from_profit_in_reports'] = 'Retirer la commission du bénéfice dans les rapports';
$lang['config_remove_points_from_profit'] = 'Retirer les points de rachat du bénéfice';
$lang['config_capture_sig_for_all_payments'] = 'signature de capture pour toutes les ventes';
$lang['config_suppliers_store_accounts'] = 'Fournisseurs Comptes magasin';
$lang['config_currency_symbol_location'] = 'Symbole monétaire Localisation';
$lang['config_before_number'] = 'Avant Nombre';
$lang['config_after_number'] = 'Après Nombre';
$lang['config_hide_desc_on_receipt'] = 'Hide Description de la réception';
$lang['config_default_percent_off'] = 'Par défaut Percent Off';
$lang['config_default_cost_plus_percent'] = 'Par défaut Cost Plus Pourcentage';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Par défaut Tier Type de pourcentage pour Excel importation';
$lang['config_override_tier_name'] = 'Substituer Tier Nom sur réception';
$lang['config_loyalty_points_without_tax'] = 'Les points de fidélité gagnés hors taxe';
$lang['config_lock_prices_suspended_sales'] = 'prix Verrouiller quand même lever la vente si elles appartiennent à un niveau';
$lang['config_remove_customer_name_from_receipt'] = 'Retirer le nom du client de la réception';
$lang['config_scale_1'] = 'UPC-12 4 chiffres de prix';
$lang['config_scale_2'] = 'UPC-12 5 Prix Digits';
$lang['config_scale_3'] = 'EAN-13 5 chiffres des prix';
$lang['config_scale_4'] = 'EAN-13 6 chiffres des prix';
$lang['config_scale_format'] = 'Échelle Barcode Format';
;
$lang['config_enable_scale'] = 'Activer Echelle';
$lang['config_scale_divide_by'] = 'Echelle Prix Diviser par';
$lang['config_do_not_force_http'] = 'Ne pas forcer HTTP en cas de besoin pour EMV Credit Card Processing';
$lang['config_logout_on_clock_out'] = 'Se connecter automatiquement lors du pointage sur';
$lang['config_user_configured_layaway_name'] = 'Substituer Nom Layaway';
$lang['config_virtual_keyboard'] = 'Virtual Keyboard (On / Off)';
$lang['config_use_tax_value_at_all_locations'] = 'Utilisez fiscales Valeurs à tous les endroits';
$lang['config_enable_ebt_payments'] = 'Activer les paiements EBT';
$lang['config_item_id_auto_increment'] = 'Item ID Incrémentation automatique Valeur de départ';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'Il y avait une erreur de changer auto_increment pour item_id';
$lang['config_item_kit_id_auto_increment'] = 'Kit Item ID Auto Increment Valeur de départ';
$lang['config_sale_id_auto_increment'] = 'Vente ID Auto Increment Valeur de départ';
$lang['config_receiving_id_auto_increment'] = 'Réception ID Incrémentation automatique Valeur de départ';
$lang['config_change_auto_increment_item_kit_id'] = 'Il y avait une erreur de changer auto_increment pour Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'Il y avait une erreur de changer auto_increment pour sale_id';
$lang['config_change_auto_increment_receiving_id'] = 'Il y avait une erreur de changer auto_increment pour receiving_id';
$lang['config_auto_increment_note'] = 'Vous ne pouvez augmenter la valeur Auto incrémentation. leur mise à jour ne sera pas affecter les ID des articles, des kits d\'articles, de vente ou receivings qui existent déjà.';

$lang['config_online_price_tier'] = 'Prix ​​en ligne Niveau';
$lang['config_woo_api_key'] = 'WooCommerce API Key';
$lang['config_email_settings_info'] = 'Paramètres de messagerie';

$lang['config_last_sync_date'] = 'Dernière date de synchronisation';
$lang['config_sync'] = 'Sync';

$lang['config_smtp_crypto'] = 'Cryptage SMTP';
$lang['config_email_protocol'] = 'Protocole d\'envoi de courrier';
$lang['config_smtp_host'] = 'Adresse du serveur SMTP';
$lang['config_smtp_user'] = 'Adresse e-mail';
$lang['config_smtp_pass'] = 'mot de passe de l\'email';
$lang['config_smtp_port'] = 'Port SMTP';
$lang['config_email_charset'] = 'Jeu de caractères';
$lang['config_email_newline'] = 'caractère newline';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'SMTP Timeout';
$lang['config_send_test_email'] = 'Send Test Email';
$lang['config_please_enter_email_to_send_test_to'] = 'S\'il vous plaît entrez l\'adresse e-mail pour envoyer un courriel à essai';
$lang['config_email_succesfully_sent'] = 'Le courriel a été envoyé avec succès';
$lang['config_taxes_info'] = 'taxes';
$lang['config_currency_info'] = 'Devise';

$lang['config_receipt_info'] = 'Le reçu';

$lang['config_barcodes_info'] = 'Barcodes';
$lang['config_customer_loyalty_info'] = 'Fidélité du consommateur';
$lang['config_price_tiers_info'] = 'Prix ​​Tiers';
$lang['config_auto_increment_ids_info'] = 'Numéros d\'identification';
$lang['config_items_info'] = 'Articles';
$lang['config_employee_info'] = 'Employé';
$lang['config_store_accounts_info'] = 'Comptes de magasins';
$lang['config_sales_info'] = 'Ventes';
$lang['config_payment_types_info'] = 'Types de paiement';
$lang['config_profit_info'] = 'Profit calcul';
$lang['reports_view_dashboard_stats'] = 'Afficher les statistiques Tableau de bord';
$lang['config_keyword_email'] = 'Paramètres de messagerie';
$lang['config_keyword_company'] = 'compagnie';
$lang['config_keyword_taxes'] = 'taxes';
$lang['config_keyword_currency'] = 'devise';
$lang['config_keyword_payment'] = 'Paiement';
$lang['config_keyword_sales'] = 'Ventes';
$lang['config_keyword_suspended_layaways'] = 'suspension des mises de côté';
$lang['config_keyword_receipt'] = 'le reçu';
$lang['config_keyword_profit'] = 'profit';
$lang['config_keyword_barcodes'] = 'codes à barres';
$lang['config_keyword_customer_loyalty'] = 'fidélité du consommateur';
$lang['config_keyword_price_tiers'] = 'niveaux de prix';
$lang['config_keyword_auto_increment'] = 'à partir incrémentation automatique base de numéros d\'identification';
$lang['config_keyword_items'] = 'articles';
$lang['config_keyword_employees'] = 'employés';
$lang['config_keyword_store_accounts'] = 'comptes magasin';
$lang['config_keyword_application_settings'] = 'paramètres de l\'application';
$lang['config_keyword_ecommerce'] = 'plate-forme de commerce électronique';
$lang['config_keyword_woocommerce'] = 'paramètres WooCommerce ecommerce';
$lang['config_billing_info'] = 'détails de facturation';
$lang['config_keyword_billing'] = 'facturation annuler la mise à jour';
$lang['config_woo_version'] = 'WooCommerce Version';

$lang['sync_phppos_item_changes'] = 'élément change de synchronisation';
$lang['config_sync_phppos_item_changes'] = 'élément change de synchronisation';
$lang['config_import_ecommerce_items_into_phppos'] = 'Importer des éléments dans phppos';
$lang['config_sync_inventory_changes'] = 'les variations de stock Sync';
$lang['config_export_phppos_tags_to_ecommerce'] = 'Exporter les tags à ecommerce';
$lang['config_export_phppos_categories_to_ecommerce'] = 'catégories à l\'exportation vers le commerce électronique';
$lang['config_export_phppos_items_to_ecommerce'] = 'articles à l\'exportation vers le commerce électronique';
$lang['config_ecommerce_cron_sync_operations'] = 'Opérations de synchronisation Ecommerce';
$lang['config_ecommerce_progress'] = 'Sync Progress';
$lang['config_woocommerce_settings_info'] = 'Paramètres WooCommerce';
$lang['config_store_location'] = 'Emplacement du magasin';
$lang['config_woo_api_secret'] = 'WooCommerce API secret';
$lang['config_woo_api_url'] = 'WooCommerce API Url';
$lang['config_ecommerce_settings_info'] = 'Plate-forme de commerce électronique';
$lang['config_ecommerce_platform'] = 'Sélectionnez la plate-forme';
$lang['config_magento_settings_info'] = 'Paramètres Magento';
$lang['confirmation_woocommerce_cron_cancel'] = 'Êtes-vous sûr de vouloir annuler la synchronisation?';
$lang['config_force_https'] = 'Exiger https pour le programme';

$lang['config_keyword_price_rules'] = 'Règles de prix';
$lang['config_disable_price_rules_dialog'] = 'dialogue Règles Prix Désactiver';
$lang['config_price_rules_info'] = 'Règles de prix';

$lang['config_prompt_to_use_points'] = 'Invite à utiliser des points lorsqu\'ils sont disponibles';



$lang['config_always_print_duplicate_receipt_all'] = 'Toujours imprimer un reçu en double pour toutes les transactions';


$lang['config_orders_and_deliveries_info'] = 'Commandes et livraisons';
$lang['config_delivery_methods'] = 'Modes de livraison';
$lang['config_shipping_providers'] = 'Fournisseurs d\'expédition';
$lang['config_expand'] = 'Développer';
$lang['config_add_delivery_rate'] = 'Ajouter le tarif de livraison';
$lang['config_add_shipping_provider'] = 'Ajouter un fournisseur de livraison';
$lang['config_delivery_rates'] = 'Taux de livraison';
$lang['config_delivery_fee'] = 'Frais de livraison';
$lang['config_keyword_orders_deliveries'] = 'Commande des livraisons de livraison';
$lang['config_delivery_fee_tax'] = 'Taxe de frais de livraison';
$lang['config_add_rate'] = 'Ajouter un tarif';
$lang['config_delivery_time'] = 'Délai de livraison en jours';
$lang['config_delivery_rate'] = 'Taux de livraison';
$lang['config_rate_name'] = 'Nom du tarif';
$lang['config_rate_fee'] = 'Tarif';
$lang['config_rate_tax'] = 'Taux d\'impôt';
$lang['config_tax_classes'] = 'Groupes d\'impôts';
$lang['config_add_tax_class'] = 'Ajouter un groupe fiscal';

$lang['config_wide_printer_receipt_format'] = 'Format de réception d\'imprimante large';

$lang['config_default_cost_plus_fixed_amount'] = 'Valeur par défaut plus montant fixe';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Valeur fixe du niveau par défaut pour l\'importation Excel';
$lang['config_default_reorder_level_when_creating_items'] = 'Niveau de réinitialisation par défaut lors de la création d\'éléments';
$lang['config_remove_customer_company_from_receipt'] = 'Supprimer le nom de la société cliente du reçu';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Importer des catégories en phpots';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Importe les tags dans les phpots';

$lang['config_shipping_zones'] = 'Zones d\'expédition';
$lang['config_add_shipping_zone'] = 'Ajouter la zone d\'expédition';
$lang['config_no_results'] = 'Aucun résultat';
$lang['config_zip_search_term'] = 'Tapez un code postal';
$lang['config_searching'] = 'Recherche...';
$lang['config_tax_class'] = 'Groupe fiscal';
$lang['config_zone'] = 'Zone';
$lang['config_zip_codes'] = 'Codes ZIP';
$lang['config_add_zip_code'] = 'Ajouter un code postal';
$lang['config_ecom_sync_logs'] = 'E-Commerce Sync Logs';
$lang['config_currency_code'] = 'Code de devise';

$lang['config_add_currency_exchange_rate'] = 'Ajouter un taux de change';
$lang['config_currency_exchange_rates'] = 'Taux d\'échange';
$lang['config_exchange_rate'] = 'Taux de change';
$lang['config_item_lookup_order'] = 'Commande de recherche d\'article';
$lang['config_item_id'] = 'ID de l\'article';
$lang['config_reset_ecommerce'] = 'Réinitialiser le commerce électronique';
$lang['config_confirm_reset_ecom'] = 'Êtes-vous sûr de vouloir réinitialiser le commerce électronique? Cela ne réinitialisera le point de vente php que les éléments ne sont plus liés';
$lang['config_reset_ecom_successfully'] = 'Vous avez réinitialisé E-Commerce avec succès';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Nombre de décimales pour la quantité sur réception';
$lang['config_enable_wic'] = 'Activer WIC';
$lang['config_store_opening_time'] = 'Heure d\'ouverture du magasin';
$lang['config_store_closing_time'] = 'Heure de fermeture des magasins';
$lang['config_limit_manual_price_adj'] = 'Limiter les ajustements et les réductions de prix manuels';
$lang['config_always_minimize_menu'] = 'Minimiser toujours le menu de la barre latérale gauche';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'NE PAS taxer les articles de service pour les livraisons';
$lang['config_paypal_me'] = 'PayPal.me Nom d\'utilisateur';
?>