<?php
$lang['config_info'] = 'Geschäfts-Einstellungen';

$lang['config_address'] = 'Geschäfts-Adresse';
$lang['config_phone'] = 'Geschäfts-Telefonnummer';
$lang['config_prefix'] = 'Verkaufs-ID Präfix';
$lang['config_website'] = 'Geschäfts-Homepage';
$lang['config_fax'] = 'Faxnummer';
$lang['config_default_tax_rate'] = 'Standard-Steuersatz %';


$lang['config_company_required'] = 'Der &gt;&gt;Geschäfts-Name&lt;&lt; ist erforderlich';

$lang['config_phone_required'] = 'Die &gt;&gt;Geschäfts-Telefonnummer&lt;&lt; ist erforderlich';
$lang['config_sale_prefix_required'] = 'Der &gt;&gt;Rechnungsnummer-Präfix&lt;&lt; ist erforderlich';
$lang['config_default_tax_rate_required'] = 'Der &gt;&gt;Standard-Steuersatz %&lt;&lt; ist erforderlich';
$lang['config_default_tax_rate_number'] = 'Der &gt;&gt;Standard-Steuersatz %&lt;&lt; muss eine Zahl sein';
$lang['config_company_website_url'] = 'Die Geschäfts-Homepage ist keine gültige (http://...)';

$lang['config_saved_unsuccessfully'] = 'Die Konfiguration konnte nicht gespeichert werden. Im Demo-Modus sind keine Konfigurations-Änderungen erlaubt oder die Steuersätze konnten nicht gespeichert werden';
$lang['config_return_policy_required'] = 'Die Rücknahme-Richtlinie ist erforderlich';
$lang['config_print_after_sale'] = 'Nach dem Verkauf Beleg drucken';
$lang['config_automatically_email_receipt'] = 'Beleg automatisch per E-Mail versenden';
$lang['config_barcode_price_include_tax'] = 'Fügen Sie Steuer auf Barcodes';
$lang['disable_confirmation_sale'] = 'Bestätigungsdialog vor Verkauf deaktivieren';


$lang['config_currency_symbol'] = 'Währungs-Symbol';
$lang['config_backup_database'] = 'Datenbank-Backup';
$lang['config_restore_database'] = 'Datenbank wiederherstellen';

$lang['config_number_of_items_per_page'] = 'Anzahl der Artikel pro Seite';
$lang['config_date_format'] = 'Datumsformat';
$lang['config_time_format'] = 'Zeitformat';



$lang['config_optimize_database'] = 'Datenbank optimieren';
$lang['config_database_optimize_successfully'] = 'Datenbank erfolgreich optimiert';
$lang['config_payment_types'] = 'Zahlungsarten';
$lang['select_sql_file'] = '.sql-Datei auswählen';
$lang['restore_heading'] = 'Hiermit können Sie die Datenbank wiederherstellen';
$lang['type_file'] = '.sql-Datei auf Ihrem Computer auswählen';
$lang['restore'] = 'wiederherstellen';
$lang['required_sql_file'] = 'Es wurde keine .sql-Datei ausgewählt';
$lang['restore_db_success'] = 'Datenbank wurde erfolgreich wiederhergestellt';
$lang['db_first_alert'] = 'Sind Sie sicher, dass die Datenbank wiederhergestellt werden soll?';
$lang['db_second_alert'] = 'Ihre aktuellen Daten gehen dabei verloren. Fortfahren?';
$lang['password_error'] = 'Falsches Passwort';
$lang['password_required'] = 'Das &gt;&gt;Passwortfeld&lt;&lt; ist erforderlich';
$lang['restore_database_title'] = 'Datenbank wiederherstellen';



$lang['config_environment'] = 'Umgebung';


$lang['config_sandbox'] = 'Sandbox';
$lang['config_production'] = 'Produktion';

$lang['config_default_payment_type'] = 'Standard-Zahlungsart';
$lang['config_speed_up_note'] = 'Nur empfehlenswert, wenn Sie mehr als 10.000 Artikel oder Kunden haben';
$lang['config_hide_signature'] = 'Unterschrift ausblenden';
$lang['config_round_cash_on_sales'] = 'Auf dem Beleg zu den nächsten 0.05 aufrunden';
$lang['config_customers_store_accounts'] = 'Kunden-Laden-Accounts aktivieren';
$lang['config_change_sale_date_when_suspending'] = 'Verkaufsdatum anpassen, wenn Verkauf zurückgestellt wird';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Verkaufsdatum anpassen, wenn zurückgestellter Verkauf abgeschlossen wird';
$lang['config_price_tiers'] = 'Preis-Stufen';
$lang['config_add_tier'] = 'Stufe hinzufügen';
$lang['config_show_receipt_after_suspending_sale'] = 'Beleg anzeigen, nachdem Verkauf zurückgestellt wurde';
$lang['config_backup_overview'] = 'Backup-Übersicht';
$lang['config_backup_overview_desc'] = 'Das regelmäßige Erstellen eines Datenbank-Backups ist sehr wichtig, allerdings kann es bei großen Datenmengen mühsam werden. Wenn Sie viele Bilder, Artikel und Verkäufe gespeichert haben, kann das die Größe Ihrer Datenbank stark beeinflussen.';
$lang['config_backup_options'] = 'Wir bieten folgende vier Optionen an, um Ihnen die Entscheidung für die richtige Backupmethode zu erleichtern';
$lang['config_backup_simple_option'] = 'Auf "Datenbank-Backup" klicken. Dies wird versuchen, Ihre gesamte Datenbank als Datei herunterzuladen. Falls Sie einen weißen Bildschirm sehen oder die Datei nicht herunterladen können, versuchen Sie bitte eine der anderen Optionen';
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin ist ein beliebtes Tool zum Verwalten von Datenbanken. Falls Sie die heruntergeladene Version mit Installer verwenden, können Sie darauf zugreifen, indem Sie diesen Link verwenden:';
$lang['config_backup_phpmyadmin_2'] = 'Ihr Benutzername ist <b>root</b> und das Passwort ist auf den Wert gesetzt, den Sie während der Installation von PHP POS angegeben haben. Wenn Sie angemeldet sind, wählen Sie Ihre Datenbank in dem Menü auf der linken Seite aus. Danach klicken Sie auf Export und senden dann das Formular ab.';
$lang['config_backup_control_panel'] = 'Falls Sie PHP POS auf einem eigenen Server installiert haben, der eine Systemsteuerung hat, wie z.B. cpanel, suchen Sie nach dem Backup-Modul, welches Ihnen meistens die Möglichkeit bietet, Backups Ihrer Datenbank herunterzuladen.';
$lang['config_backup_mysqldump'] = 'Falls Sie Zugriff auf die Shell und mysqldump auf Ihrem Server haben, können Sie versuchen, das Backup über den unten stehenden Knopf auszuführen. Anderenfalls müssen Sie eine der anderen Optionen verwenden.';
$lang['config_mysqldump_failed'] = 'Das mysqldump-Backup ist fehlgeschlagen. Dies kann durch eine Server-Beschränkung passieren oder das Tool ist nicht installiert. Bitte versuchen Sie eine andere Backup-Option';



$lang['config_looking_for_location_settings'] = 'Sie suchen nach anderen Konfigurations-Möglichkeiten? Gehen Sie zum';
$lang['config_module'] = 'Modul';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Durchschnitts-Einkaufspreis aus Lieferungen berechnen';
$lang['config_averaging_method'] = 'Durchschnitts-Berechnungs Methode';
$lang['config_historical_average'] = 'Vergangener Durchschnitt';
$lang['config_moving_average'] = 'Gleitender Durchschnitt';

$lang['config_hide_dashboard_statistics'] = 'Dashboard-Statistiken ausblenden';
$lang['config_hide_store_account_payments_in_reports'] = 'Laden-Account-Zahlungen in Berichten ausblenden';
$lang['config_id_to_show_on_sale_interface'] = 'Artikel-ID zur Anzeige in der Verkaufsoberfläche';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Autofokus auf das Artikel-Feld in der Verkaufs-/Einkaufsoberfläche';
$lang['config_automatically_show_comments_on_receipt'] = 'Kommentare automatisch auf dem Beleg anzeigen';
$lang['config_hide_customer_recent_sales'] = 'Kürzliche Verkäufe eines Kunden ausblenden';
$lang['config_spreadsheet_format'] = 'Tabellen-Format';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Gutschein-Erkennung deaktivieren';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Deaktiviere Gutschein-Abzug während eines Verkaufes';
$lang['config_always_show_item_grid'] = 'Artikel-Raster immer anzeigen';
$lang['config_legacy_detailed_report_export'] = 'Excel-Export veralteter Einträge als detaillierten Bericht aktivieren';
$lang['config_print_after_receiving'] = 'Nach dem Einkauf Beleg drucken';
$lang['config_company_info'] = 'Unternehmens-Information';


$lang['config_suspended_sales_layaways_info'] = 'Zurückgestellte Verkäufe';
$lang['config_application_settings_info'] = 'Anwendungs-Einstellungen';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Barcode auf Beleg ausblenden';
$lang['config_round_tier_prices_to_2_decimals'] = 'Preis-Stufen auf zwei Dezimalstellen runden';
$lang['config_group_all_taxes_on_receipt'] = 'Alle Steuern auf dem Beleg gruppieren';
$lang['config_receipt_text_size'] = 'Beleg-Schriftgröße';
$lang['config_small'] = 'Klein';
$lang['config_medium'] = 'Mittel';
$lang['config_large'] = 'Groß';
$lang['config_extra_large'] = 'Extra groß';
$lang['config_select_sales_person_during_sale'] = 'Verkäufer während Verkauf wählen';
$lang['config_default_sales_person'] = 'Standardmäßiger Verkäufer';
$lang['config_require_customer_for_sale'] = 'Kunde für Verkauf erforderlich';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Laden-Account-Zahlungen in den Bericht-Gesamtsummen ausblenden';
$lang['config_disable_sale_notifications'] = 'Verkaufs-Mitteilungen deaktivieren';
$lang['config_id_to_show_on_barcode'] = 'ID, welche auf dem Barcode angezeigt wird';
$lang['config_currency_denoms'] = 'Währungs-Einheiten';
$lang['config_currency_value'] = 'Wert';
$lang['config_add_currency_denom'] = 'Währungs-Einheit hinzufügen';
$lang['config_enable_timeclock'] = 'Zeiterfassung aktivieren';
$lang['config_change_sale_date_for_new_sale'] = 'Verkaufsdatum bei neuem Verkauf ändern';
$lang['config_dont_average_use_current_recv_price'] = 'Preise nicht mitteln, sondern momentanen Einkaufs-Preis verwenden';
$lang['config_number_of_recent_sales'] = 'Anzahl der kürzlich getätigten Verkäufe pro Kunde';
$lang['config_hide_suspended_recv_in_reports'] = 'Zurückgestellte Einkäufe in Berichten ausblenden';
$lang['config_calculate_profit_for_giftcard_when'] = 'Kalkuliere Gutschein-Gewinn, sobald';
$lang['config_selling_giftcard'] = 'Gutschein verkauft wird';
$lang['config_redeeming_giftcard'] = 'Gutschein eingelöst wird';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Kunden-Kontakt-Informationen auf dem Beleg ausblenden';
$lang['config_speed_up_search_queries'] = 'Suchanfragen beschleunigen?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Nach dem Druck des Belegs auf vorherige Seite umleiten';
$lang['config_enable_sounds'] = 'Sounds für Statusnachrichten aktivieren';
$lang['config_charge_tax_on_recv'] = 'Steuern auf Einkäufe berechnen';
$lang['config_report_sort_order'] = 'Sortierreihenfolge der Berichte';
$lang['config_asc'] = 'Den Ältesten zuerst';
$lang['config_desc'] = 'Den Neuesten zuerst';
$lang['config_do_not_group_same_items'] = 'Gleiche Artikel nicht gruppieren';
$lang['config_show_item_id_on_receipt'] = 'Artikel-ID auf dem Beleg anzeigen';
$lang['config_show_language_switcher'] = 'Sprach-Wechsler anzeigen';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Den Verkauf von Artikeln, die nicht auf Lager sind, verbieten';
$lang['config_number_of_items_in_grid'] = 'Anzahl der Artikel pro Seite im Artikel-Raster';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Artikelpreis nach dem Hinzufügen zum Verkauf ändern, wenn er 0 ist';
$lang['config_override_receipt_title'] = 'Beleg-Titel überschreiben';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Bei Kreditkarten-Transaktionen automatisch ein Beleg-Duplikat drucken';






$lang['config_default_type_for_grid'] = 'Standard-Typ für das Raster';
$lang['config_billing_is_managed_through_paypal'] = 'Die Abrechnung erfolgt über <a target="_blank" href="http://paypal.com">Paypal</a>. Sie können Ihre Informationen <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">hier</a> abrufen/ändern. <a href="http://phppointofsale.com/update_billing.php" target="_blank">Hier</a> finden Sie eine detaillierte Anleitung.';
$lang['config_cannot_change_language'] = 'Sprache kann nicht auf Anwendungsebene gespeichert werden. Der Standard-Admin-Mitarbeiter kann jedoch die Sprache mit dem Selektor im Kopf des Programms ändern';
$lang['disable_quick_complete_sale'] = 'Schnelle Verkaufs-Abwicklung deaktivieren';
$lang['config_fast_user_switching'] = 'Schnellen Benutzerwechsel aktivieren (ohne Passwort)';
$lang['config_require_employee_login_before_each_sale'] = 'Login eines Mitarbeiters vor jedem Verkauf erforderlich';
$lang['config_reset_location_when_switching_employee'] = 'Zurücksetzen Lage, wenn Mitarbeiter Schalt';
$lang['config_number_of_decimals'] = 'Anzahl der Dezimalstellen';
$lang['config_let_system_decide'] = 'Das System entscheiden lassen (empfohlen)';
$lang['config_thousands_separator'] = 'Tausendertrennzeichen';
$lang['config_enhanced_search_method'] = 'Erweiterte Suchmethode';
$lang['config_hide_store_account_balance_on_receipt'] = 'Laden-Account-Kontostand auf dem Beleg ausblenden';
$lang['config_decimal_point'] = 'Dezimalpunkt';
$lang['config_hide_out_of_stock_grid'] = 'Nicht vorrätige Artikel im Artikel-Raster ausblenden';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Artikel mit geringem Lagerbestand im Artikel-Modul hervorheben';
$lang['config_sort'] = 'Sortieren';
$lang['config_enable_customer_loyalty_system'] = 'Kunden-Treue-System aktivieren';
$lang['config_spend_to_point_ratio'] = 'Betrag, um Treuepunkte zu erhalten';
$lang['config_point_value'] = 'Anzeigewert der Treuepunkte (Nachkommastellen)';
$lang['config_hide_points_on_receipt'] = 'Treuepunkte auf dem Beleg ausblenden';
$lang['config_show_clock_on_header'] = 'Uhrzeit in der Kopfzeile anzeigen';
$lang['config_show_clock_on_header_help_text'] = 'Nur auf Wide-Screen-Bildschirmen sichtbar';
$lang['config_loyalty_explained_spend_amount'] = 'Betrag';
$lang['config_loyalty_explained_points_to_earn'] = 'Anzahl der Treuepunkte pro o.g. Betrag';
$lang['config_simple'] = 'Einfache Ansicht';
$lang['config_advanced'] = 'Erweiterte Ansicht';
$lang['config_loyalty_option'] = 'Treuepunkt-Programm-Optionen';
$lang['config_number_of_sales_for_discount'] = 'Anzahl der Verkäufe für Rabatt';
$lang['config_discount_percent_earned'] = 'Rabatt-Prozentsatz, wenn o.g. Anzahl Verkäufe erreicht wurde';
$lang['hide_sales_to_discount_on_receipt'] = 'Verkäufe für Rabatt auf dem Beleg ausblenden';
$lang['config_hide_price_on_barcodes'] = 'Preis auf Barcodes ausblenden';
$lang['config_always_use_average_cost_method'] = 'Immer Use Global Average Cost Preis für eine Kostenpreis des Verkaufs-Einzelteil. (NICHT überprüfen, wenn Sie wissen, was es bedeutet)';

$lang['config_test_mode_help'] = 'Verkäufe NICHT gespeichert';
$lang['config_require_customer_for_suspended_sale'] = 'Ist zum Zurückstellen von Verkäufen ein Kunde erforderlich?';
$lang['config_default_new_items_to_service'] = 'Standardmäßig neue Artikel als Service-Artikel festlegen?';






$lang['config_prompt_for_ccv_swipe'] = 'Aufforderung zur Eingabe des Sicherheitscodes, wenn die Kreditkarte durchgezogen wird';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Laden-Account deaktivieren, wenn Kredit-Limit erreicht ist';
$lang['config_mailing_labels_type'] = 'Versandaufkleberformat';
$lang['config_phppos_session_expiration'] = 'Sitzungs-Timeout';
$lang['config_hours'] = 'Stunden';
$lang['config_never'] = 'Nie';
$lang['config_on_browser_close'] = 'Beim Schließen des Browserfensters';
$lang['config_do_not_allow_below_cost'] = 'Artikel dürfen nicht unter dem Einkaufspreis verkauft werden';
$lang['config_store_account_statement_message'] = 'Verwendungszweck, welcher auf dem Kontoauszug des Laden-Accounts angezeigt wird';
$lang['config_enable_margin_calculator'] = 'Aktivieren Mark Up-Rechner';
$lang['config_enable_quick_edit'] = 'Aktivieren Sie schnell bearbeiten zu verwalten Seiten';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Original zeigen Preis auf Empfang, wenn markiert nach unten';
$lang['config_confirm_error_messages_modal'] = 'Bestätigen Sie Fehlermeldungen modale Dialoge mit';
$lang['config_remove_commission_from_profit_in_reports'] = 'Entfernen Auftrag Gewinn in Berichten';
$lang['config_remove_points_from_profit'] = 'Entfernen Sie Punkte Erlösung aus Gewinn';
$lang['config_capture_sig_for_all_payments'] = 'Capture-Signatur für alle Verkäufe';
$lang['config_suppliers_store_accounts'] = 'Lieferanten Store Accounts';
$lang['config_currency_symbol_location'] = 'Währungssymbol Ort';
$lang['config_before_number'] = 'Vor Anzahl';
$lang['config_after_number'] = 'Nach Anzahl';
$lang['config_hide_desc_on_receipt'] = 'Ausblenden Beschreibung auf Empfang';
$lang['config_default_percent_off'] = 'Standard Prozent weg';
$lang['config_default_cost_plus_percent'] = 'Standard Cost Plus Prozent';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Standard Tier Prozent Typ für Excel-Import';
$lang['config_override_tier_name'] = 'Außer Kraft setzen Tier Name auf der Quittung';
$lang['config_loyalty_points_without_tax'] = 'Treuepunkte verdient keine Steuern inklusive';
$lang['config_lock_prices_suspended_sales'] = 'Lock-Preise, wenn Desuspendieren Verkauf, auch wenn sie zu einem Tier gehören';
$lang['config_remove_customer_name_from_receipt'] = 'Entfernen Name des Kunden ab Eingang';
$lang['config_scale_1'] = 'UPC-12 4 Preisziffern';
$lang['config_scale_2'] = 'UPC-12 5 Preis Digits';
$lang['config_scale_3'] = 'EAN-13 5 Preisziffern';
$lang['config_scale_4'] = 'EAN-13 6 Preisziffern';
$lang['config_scale_format'] = 'Scale-Barcode-Format';
$lang['config_enable_scale'] = 'Aktivieren Skala';
$lang['config_scale_divide_by'] = 'Maßstab Preis Divide By';
$lang['config_do_not_force_http'] = 'Nicht HTTP erzwingen, wenn für EMV-Kreditkartenverarbeitung benötigt';
$lang['config_logout_on_clock_out'] = 'Melden Sie sich automatisch aus, wenn Taktung aus';
$lang['config_user_configured_layaway_name'] = 'Außer Kraft setzen Layaway Namen';
$lang['config_virtual_keyboard'] = 'Virtuelle Tastatur (Ein / Aus)';
$lang['config_use_tax_value_at_all_locations'] = 'Verwenden Sie Steuerwerte an allen Standorten';
$lang['config_enable_ebt_payments'] = 'Aktivieren EBT Zahlungen';
$lang['config_disable_margin_calculator'] = 'Preisspannen-Rechner deaktivieren';
$lang['config_disable_quick_edit'] = 'Schnellbearbeitung auf Verwaltungsseiten deaktivieren';
$lang['config_cancel_account'] = 'Konto schließen';
$lang['config_update_billing'] = 'Sie können Ihre Zahlungsinformationen durch Klick auf die unteren Buttons aktualisieren oder entfernen:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Unterkategorien beim Suchen oder Auswerten mit einbinden';
$lang['config_item_id_auto_increment'] = 'Item ID Autoinkrement Startwert';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'Es gab einen Fehler auto_increment für item_id Wechsel';
$lang['config_item_kit_id_auto_increment'] = 'Artikel Kit ID Autoinkrement Startwert';
$lang['config_sale_id_auto_increment'] = 'Verkauf ID Autoinkrement Startwert';
$lang['config_receiving_id_auto_increment'] = 'Empfangen ID Autoinkrement Startwert';
$lang['config_change_auto_increment_item_kit_id'] = 'Es gab einen Fehler zu ändern auto_increment für Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'Es gab einen Fehler auto_increment für sale_id Wechsel';
$lang['config_change_auto_increment_receiving_id'] = 'Es gab einen Fehler auto_increment für receiving_id Wechsel';
$lang['config_auto_increment_note'] = 'Sie können nur Autoinkrement Werte erhöhen. Aktualisieren von ihnen werden nicht-IDs für Artikel, Artikel-Kits, Vertrieb oder receivings auswirken, die bereits vorhanden sind.';

$lang['config_online_price_tier'] = 'Online-Preis Tier';
$lang['config_woo_api_key'] = 'WooCommerce API Key';
$lang['config_email_settings_info'] = 'Email Einstellungen';

$lang['config_last_sync_date'] = 'Datum der letzten Synchronisierung';
$lang['config_sync'] = 'Sync';
$lang['config_smtp_crypto'] = 'SMTP-Verschlüsselung';
$lang['config_email_protocol'] = 'Senden von Mail-Protokoll';
$lang['config_smtp_host'] = 'SMTP-Server-Adresse';
$lang['config_smtp_user'] = 'E-Mail-Addresse';
$lang['config_smtp_pass'] = 'E-Mail Passwort';
$lang['config_smtp_port'] = 'SMTP-Port';
$lang['config_email_charset'] = 'Zeichensatz';
$lang['config_email_newline'] = 'Newline Zeichen';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'SMTP Timeout';
$lang['config_send_test_email'] = 'Test Email senden';
$lang['config_please_enter_email_to_send_test_to'] = 'Bitte geben Sie E-Mail-Adresse Test E-Mail senden an';
$lang['config_email_succesfully_sent'] = 'E-Mail wurde erfolgreich gesendet';
$lang['config_taxes_info'] = 'Steuern';
$lang['config_currency_info'] = 'Währung';

$lang['config_receipt_info'] = 'Eingang';

$lang['config_barcodes_info'] = 'Barcodes';
$lang['config_customer_loyalty_info'] = 'Kundentreue';
$lang['config_price_tiers_info'] = 'Preis Tiers';
$lang['config_auto_increment_ids_info'] = 'ID-Nummern';
$lang['config_items_info'] = 'Artikel';
$lang['config_employee_info'] = 'Mitarbeiter';
$lang['config_store_accounts_info'] = 'Store Accounts';
$lang['config_sales_info'] = 'Der Umsatz';
$lang['config_payment_types_info'] = 'Bezahlmöglichkeiten';
$lang['config_profit_info'] = 'Gewinnermittlung';
$lang['reports_view_dashboard_stats'] = 'Dashboard anzeigen Statistik';
$lang['config_keyword_email'] = 'Email Einstellungen';
$lang['config_keyword_company'] = 'Unternehmen';
$lang['config_keyword_taxes'] = 'Steuern';
$lang['config_keyword_currency'] = 'Währung';
$lang['config_keyword_payment'] = 'Zahlung';
$lang['config_keyword_sales'] = 'Der Umsatz';
$lang['config_keyword_suspended_layaways'] = 'suspendiert Layaways';
$lang['config_keyword_receipt'] = 'Eingang';
$lang['config_keyword_profit'] = 'profitieren';
$lang['config_keyword_barcodes'] = 'Barcodes';
$lang['config_keyword_customer_loyalty'] = 'Kundentreue';
$lang['config_keyword_price_tiers'] = 'Preisstufen';
$lang['config_keyword_auto_increment'] = 'Start Autoinkrement-ID-Nummern-Datenbank';
$lang['config_keyword_items'] = 'Artikel';
$lang['config_keyword_employees'] = 'Mitarbeiter';
$lang['config_keyword_store_accounts'] = 'Shop-Konten';
$lang['config_keyword_application_settings'] = 'Anwendungseinstellungen';
$lang['config_keyword_ecommerce'] = 'E-Commerce-Plattform';
$lang['config_keyword_woocommerce'] = 'WooCommerce Einstellungen E-Commerce';
$lang['config_billing_info'] = 'Abrechnungsdaten';
$lang['config_keyword_billing'] = 'Abrechnung stornieren Update';
$lang['config_woo_version'] = 'WooCommerce Version';

$lang['sync_phppos_item_changes'] = 'Sync Artikel Änderungen';
$lang['config_sync_phppos_item_changes'] = 'Sync Artikel Änderungen';
$lang['config_import_ecommerce_items_into_phppos'] = 'Importieren von Objekten in phppos';
$lang['config_sync_inventory_changes'] = 'Sync Bestandsänderungen';
$lang['config_export_phppos_tags_to_ecommerce'] = 'Export-Tags zu E-Commerce';
$lang['config_export_phppos_categories_to_ecommerce'] = 'Export Kategorien E-Commerce';
$lang['config_export_phppos_items_to_ecommerce'] = 'Export Artikel zu E-Commerce';
$lang['config_ecommerce_cron_sync_operations'] = 'E-Commerce-Sync Operationen';
$lang['config_ecommerce_progress'] = 'Sync Fortschritt';
$lang['config_woocommerce_settings_info'] = 'WooCommerce Einstellungen';
$lang['config_store_location'] = 'Geschäftsort';
$lang['config_woo_api_secret'] = 'WooCommerce API Geheimnis';
$lang['config_woo_api_url'] = 'WooCommerce API URL';
$lang['config_ecommerce_settings_info'] = 'E-Commerce-Plattform';
$lang['config_ecommerce_platform'] = 'Plattform auswählen';
$lang['config_magento_settings_info'] = 'Magento-Einstellungen';
$lang['confirmation_woocommerce_cron_cancel'] = 'Sind Sie sicher, dass Sie die Synchronisierung abbrechen?';
$lang['config_force_https'] = 'Erfordern https für Programm';

$lang['config_keyword_price_rules'] = 'Preisregeln';
$lang['config_disable_price_rules_dialog'] = 'Deaktivieren Preisregeln Dialog';
$lang['config_price_rules_info'] = 'Preisregeln';

$lang['config_prompt_to_use_points'] = 'Prompt Punkte zu verwenden, wenn verfügbar';



$lang['config_always_print_duplicate_receipt_all'] = 'Drucken Sie immer doppelte Quittung für alle Transaktionen aus';


$lang['config_orders_and_deliveries_info'] = 'Aufträge und Lieferungen';
$lang['config_delivery_methods'] = 'Liefermethoden';
$lang['config_shipping_providers'] = 'Versandanbieter';
$lang['config_expand'] = 'Erweitern';
$lang['config_add_delivery_rate'] = 'Liefermenge hinzufügen';
$lang['config_add_shipping_provider'] = 'Versandversand hinzufügen';
$lang['config_delivery_rates'] = 'Lieferkosten';
$lang['config_delivery_fee'] = 'Liefergebühr';
$lang['config_keyword_orders_deliveries'] = 'Bestellt Lieferungen';
$lang['config_delivery_fee_tax'] = 'Liefergebühr Steuer';
$lang['config_add_rate'] = 'Rate hinzufügen';
$lang['config_delivery_time'] = 'Lieferzeit in Tagen';
$lang['config_delivery_rate'] = 'Zustelltarif';
$lang['config_rate_name'] = 'Rate Name';
$lang['config_rate_fee'] = 'Bewerbungsgebühr';
$lang['config_rate_tax'] = 'Steuern zahlen';
$lang['config_tax_classes'] = 'Steuergruppen';
$lang['config_add_tax_class'] = 'Steuergruppe hinzufügen';

$lang['config_wide_printer_receipt_format'] = 'Wide Printer Receipt Format';

$lang['config_default_cost_plus_fixed_amount'] = 'Default Cost Plus Fester Betrag';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Standard-fester fester Betrag für Excel-Import';
$lang['config_default_reorder_level_when_creating_items'] = 'Default Reorder Level beim Erstellen von Items';
$lang['config_remove_customer_company_from_receipt'] = 'Kundennummer aus dem Beleg entfernen';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Importieren Sie Kategorien in Phppos';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Importiert Tags in Phppos';

$lang['config_shipping_zones'] = 'Versandzonen';
$lang['config_add_shipping_zone'] = 'Fügen Sie Versandzone hinzu';
$lang['config_no_results'] = 'Keine Ergebnisse';
$lang['config_zip_search_term'] = 'Geben Sie eine Postleitzahl ein';
$lang['config_searching'] = 'Suchen ...';
$lang['config_tax_class'] = 'Steuergruppe';
$lang['config_zone'] = 'Zone';

$lang['config_zip_codes'] = 'Postleitzahlen';
$lang['config_add_zip_code'] = 'Postleitzahl hinzufügen';
$lang['config_ecom_sync_logs'] = 'E-Commerce-Synchronisierungsprotokolle';
$lang['config_currency_code'] = 'Währungscode';

$lang['config_add_currency_exchange_rate'] = 'Währungswechselkurs hinzufügen';
$lang['config_currency_exchange_rates'] = 'Wechselkurse';
$lang['config_exchange_rate'] = 'Tauschrate';
$lang['config_item_lookup_order'] = 'Artikelsuche';
$lang['config_item_id'] = 'Artikel Identifikationsnummer';
$lang['config_reset_ecommerce'] = 'E-Commerce zurücksetzen';
$lang['config_confirm_reset_ecom'] = 'Sind Sie sicher, dass Sie E-Commerce zurücksetzen möchten? Dies wird nur php Punkt des Verkaufs zurücksetzen, damit Artikel nicht mehr verknüpft sind';
$lang['config_reset_ecom_successfully'] = 'Sie haben E-Commerce erfolgreich zurückgesetzt';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Anzahl der Dezimalstellen für Anzahl bei Empfang';
$lang['config_enable_wic'] = 'WIC aktivieren';
$lang['config_store_opening_time'] = 'Store Öffnungszeit';
$lang['config_store_closing_time'] = 'Speichern der Schließzeit';
$lang['config_limit_manual_price_adj'] = 'Begrenzung manuelle Preisanpassungen und Rabatte';
$lang['config_always_minimize_menu'] = 'Immer Minimieren Linke Seite Bar Menü';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'Steuern Sie keine Serviceartikel für Lieferungen';
$lang['config_paypal_me'] = 'PayPal.me Benutzername';
?>