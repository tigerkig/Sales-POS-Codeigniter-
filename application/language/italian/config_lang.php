<?php
$lang['config_info']='Configurazione negozio';

$lang['config_address']='Indirizzo Azienda';
$lang['config_phone']='Telefono';

$lang['config_fax']='Fax';
$lang['config_default_tax_rate']='Tariffa Tassa Predefinita %';


$lang['config_company_required']='Nome azienda è un campo obbligatorio';

$lang['config_phone_required']='Telefono azienda è un campo obbligatorio';
$lang['config_default_tax_rate_required']='Tariffa Tassa Predefinita è un campo obbligatorio';
$lang['config_default_tax_rate_number']='Tariffa Tassa Predefinita deve essere un numero';
$lang['config_company_website_url']='Il website azienda non è un valido URL (http://...)';

$lang['config_saved_unsuccessfully']='Impossibile salvare la configurazione. Le modifiche di configurazione non sono ammessi in modalità demo o le tasse non sono state salvate correttamente';
$lang['config_return_policy_required']='Politica di ritorno è un campo obbligatorio';
$lang['config_print_after_sale']='Stampa ricevuta dopo la vendita';
$lang['config_automatically_email_receipt']='Email automaticamente la ricevuta';
$lang['config_barcode_price_include_tax']='Include tassa sui codici a barre';
$lang['disable_confirmation_sale']='Disattivare la conferma per la vendita completa';


$lang['config_currency_symbol'] = 'Simbolo Valuta';
$lang['config_backup_database'] = 'Database Backup';
$lang['config_restore_database'] = 'Ripristina Database';

$lang['config_number_of_items_per_page'] = 'Numero prodotti per pagina';
$lang['config_date_format'] = 'Formato Data';
$lang['config_time_format'] = 'Formato Ora';



$lang['config_optimize_database'] = 'Ottimizza Database';
$lang['config_database_optimize_successfully'] = 'Database Ottimizzato';
$lang['config_payment_types'] = 'Tipo Pagamenti';
$lang['select_sql_file'] = 'File selezionare. sql';

$lang['restore_heading'] = 'Questo consente di ripristinare il database';

$lang['type_file'] = 'selezionare il file. sql dal computer';

$lang['restore'] = 'ripristina';

$lang['required_sql_file'] = 'No sql file selezionato';

$lang['restore_db_success'] = 'DataBase è stato ripristinato';

$lang['db_first_alert'] = 'Sei sicuro di ripristino del database?';
$lang['db_second_alert'] = 'I presenti dati saranno persi, continuare?';
$lang['password_error'] = 'Password incorretta';
$lang['password_required'] = 'Campo password non può essere vuoto';
$lang['restore_database_title'] = 'Ripristina Database';



$lang['config_environment'] = 'Ambiente';


$lang['config_sandbox'] = 'Sandbox';
$lang['config_production'] = 'Produzione';

$lang['config_default_payment_type'] = 'Tipo di pagamento predefinito';
$lang['config_speed_up_note'] = 'Consigliato solo se si dispone di più di 10.000 articoli o clienti';
$lang['config_hide_signature'] = 'Nascondi Firmae';
$lang['config_round_cash_on_sales'] = 'Arrotonda al più vicino 0.5';
$lang['config_prefix'] = 'Vendita ID Prefix';
$lang['config_sale_prefix_required'] = 'Prefisso ID Sale è un campo obbligatorio';
$lang['config_customers_store_accounts'] = 'Clienti Negozio Conti';
$lang['config_change_sale_date_when_suspending'] = 'Cambiare la data vendita quando sospende la vendita';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Cambiare data di cessione al momento di compilare vendita sospesa';
$lang['config_price_tiers'] = 'Livelli di prezzo';
$lang['config_add_tier'] = 'Aggiungi tier';
$lang['config_show_receipt_after_suspending_sale'] = 'Mostra ricevuta dopo aver sospeso la vendita';
$lang['config_backup_overview'] = 'Panoramica del backup';
$lang['config_backup_overview_desc'] = 'Il backup dei dati è molto importante, ma può essere fastidioso con grandi quantità di dati. Se hai un sacco di immagini, oggetti e vendite questo può aumentare la dimensione del database.';
$lang['config_backup_options'] = 'Offriamo molte opzioni per il backup per aiutarvi a decidere come procedere';
$lang['config_backup_simple_option'] = 'Cliccando su &quot;Backup database&quot;. Questo tenterà di scaricare l\'intero database in un file. Se si ottiene una schermata vuota o non riesci a scaricare il file, provare una delle altre opzioni.';
$lang['config_backup_phpmyadmin_1'] = 'PhpMyAdmin è un popolare strumento per la gestione dei database. Se si utilizza la versione download con installer, si può accedere andando a';
$lang['config_backup_phpmyadmin_2'] = 'Il tuo nome utente è root e la password è quello che hai usato durante l\'installazione iniziale di PHP POS. Una volta effettuato il login selezionare il database dal pannello di sinistra. Quindi selezionare l\'esportazione e poi inviare il modulo.';
$lang['config_backup_control_panel'] = 'Se avete installato sul proprio server che dispone di un pannello di controllo come cPanel, cercare il modulo di backup che spesso permetterà di scaricare copie di backup del database.';
$lang['config_backup_mysqldump'] = 'Se si ha accesso alla shell e mysqldump sul server, si può provare a eseguirlo cliccando sul link in basso. In caso contrario, sarà necessario provare altre opzioni.';
$lang['config_mysqldump_failed'] = 'di backup mysqldump non è riuscita. Questo potrebbe essere dovuto ad una restrizione del server o il comando potrebbe non essere disponibile. Prova con un altro metodo di backup';



$lang['config_looking_for_location_settings'] = 'Alla ricerca di altre opzioni di configurazione? Vai a';
$lang['config_module'] = 'Modulo';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Calcola il costo medio Prezzo da Ricevimenti';
$lang['config_averaging_method'] = 'Metodo di calcolo della media';
$lang['config_historical_average'] = 'Media storica';
$lang['config_moving_average'] = 'Media mobile';

$lang['config_hide_dashboard_statistics'] = 'Hide Dashboard statistiche';
$lang['config_hide_store_account_payments_in_reports'] = 'Pagamenti account Hide negozio a Report';
$lang['config_id_to_show_on_sale_interface'] = 'ID voce per visualizzare sull\'interfaccia di vendita';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Auto Focus Sul Campo Voce Quando si utilizza Vendite / Ricevimenti Interfacce';
$lang['config_automatically_show_comments_on_receipt'] = 'Mostra automaticamente Commenti su Receipt';
$lang['config_hide_customer_recent_sales'] = 'Hide vendite recenti per clienti';
$lang['config_spreadsheet_format'] = 'Foglio Formato';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Disattivare il rilevamento Giftcard';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Disattivare giftcard sottrazione quando si utilizza giftcard durante la vendita';
$lang['config_always_show_item_grid'] = 'Mostra sempre Griglia articolo';
$lang['config_legacy_detailed_report_export'] = 'Legacy rapporto dettagliato Excel Export';
$lang['config_print_after_receiving'] = 'Ricevuta di stampa dopo aver ricevuto';
$lang['config_company_info'] = 'Informazioni sulla società';


$lang['config_suspended_sales_layaways_info'] = 'Sospeso Vendite / Layaways';
$lang['config_application_settings_info'] = 'Impostazioni applicazione';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Nascondi codice a barre sugli incassi';
$lang['config_round_tier_prices_to_2_decimals'] = 'Rotonde tier Prezzi a 2 decimali';
$lang['config_group_all_taxes_on_receipt'] = 'Gruppo di tutte le tasse al ricevimento';
$lang['config_receipt_text_size'] = 'Ricevuta la dimensione del testo';
$lang['config_small'] = 'Piccolo';
$lang['config_medium'] = 'Medio';
$lang['config_large'] = 'Grande';
$lang['config_extra_large'] = 'Extra grande';
$lang['config_select_sales_person_during_sale'] = 'Selezionare la persona di vendite durante la vendita';
$lang['config_default_sales_person'] = 'Persona di vendite di default';
$lang['config_require_customer_for_sale'] = 'Richiedi cliente per la vendita';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Nascondi conto deposito pagamenti totali del report';
$lang['config_disable_sale_notifications'] = 'Disattivare le notifiche di vendita';
$lang['config_id_to_show_on_barcode'] = 'ID per mostrare il codice a barre';
$lang['config_currency_denoms'] = 'Denominazioni di valuta';
$lang['config_currency_value'] = 'Valore Valuta';
$lang['config_add_currency_denom'] = 'Aggiungere valuta di denominazione';
$lang['config_enable_timeclock'] = 'Abilita orologio';
$lang['config_change_sale_date_for_new_sale'] = 'Change vendita Data In Nuova vendita';
$lang['config_dont_average_use_current_recv_price'] = 'Non media, utilizzare prezzo corrente ricevuta';
$lang['config_number_of_recent_sales'] = 'Numero di vendite recenti dal cliente per mostrare';
$lang['config_hide_suspended_recv_in_reports'] = 'Hide Ricevimenti sospesi nei report';
$lang['config_calculate_profit_for_giftcard_when'] = 'Calcola Gift Card Profit Quando';
$lang['config_selling_giftcard'] = 'Vendere Gift Card';
$lang['config_redeeming_giftcard'] = 'Gift Card Redentore';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Rimuovere informazioni di contatto del cliente dal ricevimento';
$lang['config_speed_up_search_queries'] = 'Accelerare le query di ricerca?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Reindirizzare la vendita o la ricezione di schermo dopo la stampa il ricevimento';
$lang['config_enable_sounds'] = 'Abilita suoni per i messaggi di stato';
$lang['config_charge_tax_on_recv'] = 'Carico fiscale su Ricevimenti';
$lang['config_report_sort_order'] = 'Rapporto Ordine';
$lang['config_asc'] = 'Prima i più vecchi';
$lang['config_desc'] = 'Recente primo';
$lang['config_do_not_group_same_items'] = 'Non raggruppare gli elementi che sono gli stessi';
$lang['config_show_item_id_on_receipt'] = 'Mostra voce id al ricevimento';
$lang['config_show_language_switcher'] = 'Mostra Language Switcher';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Non permettere gli articoli fuori stock per essere venduti';
$lang['config_number_of_items_in_grid'] = 'Numero di articoli per pagina nella griglia';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Modifica articolo prezzo se 0 dopo l\'aggiunta di vendita';
$lang['config_override_receipt_title'] = 'Titolo ricevuta Override';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Stampare automaticamente ricevuta duplicato per le transazioni con carta di credito';






$lang['config_default_type_for_grid'] = 'Tipo predefinito per griglia';
$lang['config_billing_is_managed_through_paypal'] = 'Fatturazione è gestita attraverso <a target="_blank" href="http://paypal.com">Paypal</a>. È possibile annullare l\'iscrizione cliccando <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">qui</a>. <a href="http://phppointofsale.com/update_billing.php" target="_blank">È possibile aggiornare la fatturazione qui</a>';
$lang['config_cannot_change_language'] = 'Il linguaggio non può essere salvata a livello di applicazione. Tuttavia il dipendente di default amministratore può cambiare la lingua tramite il selettore nell\'intestazione del programma';
$lang['disable_quick_complete_sale'] = 'Disabilita vendita rapida completo';
$lang['config_fast_user_switching'] = 'Abilita cambio utente rapido (password non richiesta)';
$lang['config_require_employee_login_before_each_sale'] = 'Richiedere login dipendente prima di ogni vendita';
$lang['config_reset_location_when_switching_employee'] = 'Ripristina posizione quando si passa dipendente';
$lang['config_number_of_decimals'] = 'Numero di decimali';
$lang['config_let_system_decide'] = 'Sia il sistema a decidere (scelta consigliata)';
$lang['config_thousands_separator'] = 'Migliaia separatore';
$lang['config_enhanced_search_method'] = 'Metodo di ricerca avanzata';
$lang['config_hide_store_account_balance_on_receipt'] = 'Nascondi archivio account equilibrio al ricevimento';
$lang['config_decimal_point'] = 'Punto decimale';
$lang['config_hide_out_of_stock_grid'] = 'Nascondere gli articoli fuori stock in rete';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Evidenziare le voci basse scorte di modulo articoli';
$lang['config_sort'] = 'Sorta';
$lang['config_enable_customer_loyalty_system'] = 'Abilita sistema di Customer Loyalty';
$lang['config_spend_to_point_ratio'] = 'Spendere importo al punto rapporto';
$lang['config_point_value'] = 'Punteggio';
$lang['config_hide_points_on_receipt'] = 'Nascondi punti al ricevimento';
$lang['config_show_clock_on_header'] = 'Mostra Orologio in Intestazione';
$lang['config_show_clock_on_header_help_text'] = 'Questo è visibile solo su schermi larghi';
$lang['config_loyalty_explained_spend_amount'] = 'Inserisci l\'importo da spendere';
$lang['config_loyalty_explained_points_to_earn'] = 'Inserisci punti da guadagnare';
$lang['config_simple'] = 'Semplice';
$lang['config_advanced'] = 'Avanzate';
$lang['config_loyalty_option'] = 'Opzione Programma Fedeltà';
$lang['config_number_of_sales_for_discount'] = 'Numero di vendite per lo sconto';
$lang['config_discount_percent_earned'] = 'Per cento di sconto guadagnato quando raggiunge le vendite';
$lang['hide_sales_to_discount_on_receipt'] = 'Nascondere le vendite di sconto sulla ricevuta';
$lang['config_hide_price_on_barcodes'] = 'Hide prezzo su codici a barre';
$lang['config_always_use_average_cost_method'] = 'Usa sempre globale media di prezzi di costo per una vendita del lotto di prezzi di costo. (NON verificare se non si sa che cosa significa)';

$lang['config_test_mode_help'] = 'Le vendite non salvati';
$lang['config_require_customer_for_suspended_sale'] = 'Richiedere cliente per vendita sospesa';
$lang['config_default_new_items_to_service'] = 'Predefinito Nuovi oggetti come elementi di servizio';






$lang['config_prompt_for_ccv_swipe'] = 'Richiedi per CCV quando strisciata della carta di credito';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Disabilita conto deposito quando oltre il limite di credito';
$lang['config_mailing_labels_type'] = 'Etichette Postali Formato';
$lang['config_phppos_session_expiration'] = 'Scadenza della sessione';
$lang['config_hours'] = 'Ore';
$lang['config_never'] = 'Mai';
$lang['config_on_browser_close'] = 'Su Browser Chiudi';
$lang['config_do_not_allow_below_cost'] = 'Non consentono elementi per essere venduti sotto costo';
$lang['config_store_account_statement_message'] = 'Conservare Estratto conto Messaggio';
$lang['config_enable_margin_calculator'] = 'Abilita Mark Up Calcolatrice';
$lang['config_enable_quick_edit'] = 'Abilita modifica rapida su gestire le pagine';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Chiedi il prezzo originale, al ricevimento se segnato';
$lang['config_cancel_account'] = 'Cancellare account';
$lang['config_update_billing'] = 'È possibile aggiornare e cancellare i suoi dati di fatturazione facendo clic sui pulsanti qui sotto:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Includi categorie bambino durante la ricerca o la segnalazione';
$lang['config_confirm_error_messages_modal'] = 'Confermare i messaggi di errore utilizzando le finestre di dialogo modali';
$lang['config_remove_commission_from_profit_in_reports'] = 'Rimuovere commissione dal profitto nei rapporti';
$lang['config_remove_points_from_profit'] = 'Rimuovere i punti redenzione dal profitto';
$lang['config_capture_sig_for_all_payments'] = 'firma di cattura per tutte le vendite';
$lang['config_suppliers_store_accounts'] = 'Fornitori Conservare Conti';
$lang['config_currency_symbol_location'] = 'Simbolo monetario Località';
$lang['config_before_number'] = 'prima Number';
$lang['config_after_number'] = 'dopo Number';
$lang['config_hide_desc_on_receipt'] = 'Nascondi la descrizione su Ricevuta';
$lang['config_default_percent_off'] = 'Cento di sconto predefinito';
$lang['config_default_cost_plus_percent'] = 'Cost Plus percentuale di default';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Predefinito Livello Tipo percentuale per Excel importazione';
$lang['config_override_tier_name'] = 'Override Livello Nome sulla ricevuta';
$lang['config_loyalty_points_without_tax'] = 'I punti fedeltà guadagnati escluse le imposte';
$lang['config_lock_prices_suspended_sales'] = 'prezzi bloccarsi quando Riattivare vendita, anche se appartengono a un livello';
$lang['config_remove_customer_name_from_receipt'] = 'Rimuovere Nome cliente dal ricevimento';
$lang['config_scale_1'] = 'UPC-12 4 cifre dei prezzi';
$lang['config_scale_2'] = 'UPC-12 5 cifre Prezzo';
$lang['config_scale_3'] = 'EAN-13 5 cifre dei prezzi';
$lang['config_scale_4'] = 'EAN-13 6 cifre dei prezzi';
$lang['config_scale_format'] = 'Scala di codici a barre Formato';
;
$lang['config_enable_scale'] = 'Abilita Scala';
$lang['config_scale_divide_by'] = 'Scala Prezzo Divide By';
$lang['config_do_not_force_http'] = 'Non forzare HTTP in caso di necessità per EMV carta di credito Processing';
$lang['config_logout_on_clock_out'] = 'Esci automaticamente quando clocking fuori';
$lang['config_user_configured_layaway_name'] = 'Override Nome Layaway';
$lang['config_virtual_keyboard'] = 'Tastiera Virtuale (On / Off)';
$lang['config_use_tax_value_at_all_locations'] = 'Utilizzare valori fiscali a TUTTI i luoghi';
$lang['config_enable_ebt_payments'] = 'Abilita pagamenti EBT';
$lang['config_item_id_auto_increment'] = 'Articolo ID Incremento automatico valore di partenza';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'Si è verificato un errore durante il cambiamento auto_increment per item_id';
$lang['config_item_kit_id_auto_increment'] = 'Kit Item ID incremento automatico valore di partenza';
$lang['config_sale_id_auto_increment'] = 'Vendita ID Incremento automatico valore di partenza';
$lang['config_receiving_id_auto_increment'] = 'Ricezione ID Incremento automatico valore di partenza';
$lang['config_change_auto_increment_item_kit_id'] = 'Si è verificato un errore durante il cambiamento auto_increment per Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'Si è verificato un errore durante il cambiamento auto_increment per sale_id';
$lang['config_change_auto_increment_receiving_id'] = 'Si è verificato un errore durante il cambiamento auto_increment per receiving_id';
$lang['config_auto_increment_note'] = 'È possibile aumentare solo i valori incremento automatico. aggiornandoli non influenzerà gli ID per gli articoli, kit voce, vendite o Ricevimenti che già esistono.';

$lang['config_online_price_tier'] = 'In linea Tier Prezzo';
$lang['config_woo_api_key'] = 'Woocommerce API Key';
$lang['config_email_settings_info'] = 'Impostazioni e-mail';

$lang['config_last_sync_date'] = 'Ultima sincronizzazione Data';
$lang['config_sync'] = 'Sync';
$lang['config_smtp_crypto'] = 'crittografia SMTP';
$lang['config_email_protocol'] = 'Posta protocollo invio';
$lang['config_smtp_host'] = 'Indirizzo server SMTP';
$lang['config_smtp_user'] = 'Indirizzo email';
$lang['config_smtp_pass'] = 'E-mail password';
$lang['config_smtp_port'] = 'porta SMTP';
$lang['config_email_charset'] = 'set di caratteri';
$lang['config_email_newline'] = 'carattere newline';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'Timeout SMTP';
$lang['config_send_test_email'] = 'Send Test Email';
$lang['config_please_enter_email_to_send_test_to'] = 'Si prega di inserire indirizzo di posta elettronica per inviare e-mail di prova per';
$lang['config_email_succesfully_sent'] = 'E-mail è stata inviata con successo';
$lang['config_taxes_info'] = 'Le tasse';
$lang['config_currency_info'] = 'Moneta';

$lang['config_receipt_info'] = 'Ricevuta';

$lang['config_barcodes_info'] = 'Codici a barre';
$lang['config_customer_loyalty_info'] = 'Fedeltà del cliente';
$lang['config_price_tiers_info'] = 'Tiers Prezzo';
$lang['config_auto_increment_ids_info'] = 'numeri di ID';
$lang['config_items_info'] = 'Elementi';
$lang['config_employee_info'] = 'Dipendente';
$lang['config_store_accounts_info'] = 'Conti Negozio';
$lang['config_sales_info'] = 'I saldi';
$lang['config_payment_types_info'] = 'Modalità di pagamento';
$lang['config_profit_info'] = 'Calcolo di profitto';
$lang['reports_view_dashboard_stats'] = 'Vista Dashboard statistiche';
$lang['config_keyword_email'] = 'impostazioni e-mail';
$lang['config_keyword_company'] = 'azienda';
$lang['config_keyword_taxes'] = 'le tasse';
$lang['config_keyword_currency'] = 'moneta';
$lang['config_keyword_payment'] = 'pagamento';
$lang['config_keyword_sales'] = 'i saldi';
$lang['config_keyword_suspended_layaways'] = 'layaways sospesi';
$lang['config_keyword_receipt'] = 'ricevuta';
$lang['config_keyword_profit'] = 'profitto';
$lang['config_keyword_barcodes'] = 'codici a barre';
$lang['config_keyword_customer_loyalty'] = 'fedeltà del cliente';
$lang['config_keyword_price_tiers'] = 'livelli di prezzo';
$lang['config_keyword_auto_increment'] = 'a partire incremento automatico del database numeri di ID';
$lang['config_keyword_items'] = 'elementi';
$lang['config_keyword_employees'] = 'dipendenti';
$lang['config_keyword_store_accounts'] = 'conti negozio';
$lang['config_keyword_application_settings'] = 'impostazioni dell\'applicazione';
$lang['config_keyword_ecommerce'] = 'piattaforma di e-commerce';
$lang['config_keyword_woocommerce'] = 'impostazioni woocommerce e-commerce';
$lang['config_billing_info'] = 'Informazioni di fatturazione';
$lang['config_keyword_billing'] = 'fatturazione annullare l\'aggiornamento';
$lang['config_woo_version'] = 'WooCommerce Version';

$lang['sync_phppos_item_changes'] = 'Sync voce cambia';
$lang['config_sync_phppos_item_changes'] = 'Sync voce cambia';
$lang['config_import_ecommerce_items_into_phppos'] = 'Importa gli elementi in phppos';
$lang['config_sync_inventory_changes'] = 'variazioni d\'inventario Sync';
$lang['config_export_phppos_tags_to_ecommerce'] = 'Esporta tag per e-commerce';
$lang['config_export_phppos_categories_to_ecommerce'] = 'categorie di esportazione verso e-commerce';
$lang['config_export_phppos_items_to_ecommerce'] = 'articoli di esportazione verso e-commerce';
$lang['config_ecommerce_cron_sync_operations'] = 'Le operazioni di sincronizzazione e-commerce';
$lang['config_ecommerce_progress'] = 'Sync Progress';
$lang['config_woocommerce_settings_info'] = 'Impostazioni Woocommerce';
$lang['config_store_location'] = 'Posizione del negozio';
$lang['config_woo_api_secret'] = 'Woocommerce API Segreto';
$lang['config_woo_api_url'] = 'Woocommerce API URL';
$lang['config_ecommerce_settings_info'] = 'Piattaforma e-commerce';
$lang['config_ecommerce_platform'] = 'Selezionare Piattaforma';
$lang['config_magento_settings_info'] = 'Impostazioni Magento';
$lang['confirmation_woocommerce_cron_cancel'] = 'Sei sicuro di voler annullare la sincronizzazione?';
$lang['config_force_https'] = 'Richiede https per il programma';

$lang['config_keyword_price_rules'] = 'Regole Prezzo';
$lang['config_disable_price_rules_dialog'] = 'Disabilita finestra di dialogo Regole Prezzo';
$lang['config_price_rules_info'] = 'Regole Prezzo';

$lang['config_prompt_to_use_points'] = 'Prompt di utilizzare i punti quando disponibile';



$lang['config_always_print_duplicate_receipt_all'] = 'Stampa sempre la ricevuta duplicata per tutte le transazioni';


$lang['config_orders_and_deliveries_info'] = 'Ordini e consegne';
$lang['config_delivery_methods'] = 'Metodi di consegna';
$lang['config_shipping_providers'] = 'Fornitori di Spedizione';
$lang['config_expand'] = 'Espandere';
$lang['config_add_delivery_rate'] = 'Aggiungi la velocità di consegna';
$lang['config_add_shipping_provider'] = 'Aggiungi il fornitore di spedizioni';
$lang['config_delivery_rates'] = 'Tariffe di consegna';
$lang['config_delivery_fee'] = 'Tassa di consegna';
$lang['config_keyword_orders_deliveries'] = 'Consegna le consegne di consegna';
$lang['config_delivery_fee_tax'] = 'Tassa di consegna';
$lang['config_add_rate'] = 'Aggiungi tariffa';
$lang['config_delivery_time'] = 'Tempo di consegna in giorni';
$lang['config_delivery_rate'] = 'Frequenza di consegna';
$lang['config_rate_name'] = 'Nome del tasso';
$lang['config_rate_fee'] = 'Tariffa tariffaria';
$lang['config_rate_tax'] = 'Tasso di imposta';
$lang['config_tax_classes'] = 'Gruppi fiscali';
$lang['config_add_tax_class'] = 'Aggiungi gruppo fiscale';

$lang['config_wide_printer_receipt_format'] = 'Formato di ricevuta stampante larga';

$lang['config_default_cost_plus_fixed_amount'] = 'Importo predefinito Plus Plus fisso';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Importo fisso di livello predefinito per l\'importazione di Excel';
$lang['config_default_reorder_level_when_creating_items'] = 'Livello di riordino predefinito durante la creazione di elementi';
$lang['config_remove_customer_company_from_receipt'] = 'Rimuovere il nome della società cliente dalla ricevuta';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Importa categorie in phppos';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Importa i tag in phppos';

$lang['config_shipping_zones'] = 'Zone di spedizione';
$lang['config_add_shipping_zone'] = 'Aggiungi zona di spedizione';
$lang['config_no_results'] = 'Nessun risultato';
$lang['config_zip_search_term'] = 'Digita un codice postale';
$lang['config_searching'] = 'Ricerca in corso ...';
$lang['config_tax_class'] = 'Gruppo fiscale';
$lang['config_zone'] = 'Zona';

$lang['config_zip_codes'] = 'Codici di avviamento postale';
$lang['config_add_zip_code'] = 'Aggiungi codice postale';
$lang['config_ecom_sync_logs'] = 'Registri di sincronizzazione di E-Commerce';
$lang['config_currency_code'] = 'Codice valuta';

$lang['config_add_currency_exchange_rate'] = 'Aggiungere la Cambio Valuta';
$lang['config_currency_exchange_rates'] = 'Tassi di cambio';
$lang['config_exchange_rate'] = 'Tasso di cambio';
$lang['config_item_lookup_order'] = 'Ordine di ricerca dell\'articolo';
$lang['config_item_id'] = 'Numero identificativo dell\'oggetto';
$lang['config_reset_ecommerce'] = 'Resettare l\'E-Commerce';
$lang['config_confirm_reset_ecom'] = 'Sei sicuro di voler resettare l\'e-commerce? Questo ripristina solo il punto di vendita php in modo che gli elementi non siano più collegati';
$lang['config_reset_ecom_successfully'] = 'Hai resettato E-Commerce con successo';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Numero di decimali per la quantità alla ricevuta';
$lang['config_enable_wic'] = 'Attiva WIC';
$lang['config_store_opening_time'] = 'Memorizza l\'ora di apertura';
$lang['config_store_closing_time'] = 'Conservare il tempo di chiusura';
$lang['config_limit_manual_price_adj'] = 'Limitare i rimborsi e le riduzioni dei prezzi manuali';
$lang['config_always_minimize_menu'] = 'Sempre ridurre al minimo il menu della barra laterale sinistra';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'NON fa servizio fiscale per le consegne';
$lang['config_paypal_me'] = 'PayPal.me Nome utente';
?>