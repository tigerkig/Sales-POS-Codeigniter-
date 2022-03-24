<?php
$lang['config_info']='Store Configuration Information';

$lang['config_address']='Company Address';
$lang['config_phone']='Company Phone';
$lang['config_prefix']='Sale ID Prefix';

$lang['config_fax']='Fax';
$lang['config_default_tax_rate']='Default Tax Rate %';


$lang['config_company_required']='Company name is a required field';

$lang['config_phone_required']='Company phone is a required field';
$lang['config_sale_prefix_required']='Sale ID prefix is a required field';
$lang['config_default_tax_rate_required']='The default tax rate is a required field';
$lang['config_default_tax_rate_number']='The default tax rate must be a number';
$lang['config_company_website_url']='Company website is not a valid URL (http://...)';

$lang['config_saved_unsuccessfully']='Failed to save configuration. Configuration changes are not allowed in demo mode or taxes weren\'t saved correctly';
$lang['config_return_policy_required']='Return policy is a required field';
$lang['config_print_after_sale']='Print receipt after sale';
$lang['config_automatically_email_receipt']='Automatically Email receipt';
$lang['config_barcode_price_include_tax']='Include tax on barcodes';
$lang['disable_confirmation_sale']='Disable confirmation for complete sale';


$lang['config_currency_symbol'] = 'Currency Symbol';
$lang['config_backup_database'] = 'Backup Database';
$lang['config_restore_database'] = 'Restore Database';

$lang['config_number_of_items_per_page'] = 'Number Of Items Per Page';
$lang['config_date_format'] = 'Date Format';
$lang['config_time_format'] = 'Time Format';



$lang['config_optimize_database'] = 'Optimize Database';
$lang['config_database_optimize_successfully'] = 'Optimized Database Successfully';
$lang['config_payment_types'] = 'Payment Types';
$lang['select_sql_file'] = 'select .sql file';

$lang['restore_heading'] = 'This allows you to restore your database';

$lang['type_file'] = 'select .sql file from your computer';

$lang['restore'] = 'restore';

$lang['required_sql_file'] = 'No sql file is selected';

$lang['restore_db_success'] = 'DataBase is restored successfully';

$lang['db_first_alert'] = 'Are you sure of restoring the database?';
$lang['db_second_alert'] = 'Present data will be lost , continue?';
$lang['password_error'] = 'Password incorrect';
$lang['password_required'] = 'Password field cannot be blank';
$lang['restore_database_title'] = 'Restore Database';



$lang['config_environment'] = 'Environment';


$lang['config_sandbox'] = 'Sandbox';
$lang['config_production'] = 'Production';

$lang['config_default_payment_type'] = 'Default Payment Type';
$lang['config_speed_up_note'] = 'Only recommend if you have more than 10,000 items or customers';
$lang['config_hide_signature'] = 'Hide Signature';
$lang['config_round_cash_on_sales'] = 'Round to nearest .05 on receipt';
$lang['config_customers_store_accounts'] = 'Customers Store Accounts';
$lang['config_change_sale_date_when_suspending'] = 'Change sale date when suspending sale';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Change sale date when completing suspended sale';
$lang['config_price_tiers'] = 'Price tiers';
$lang['config_add_tier'] = 'Add tier';
$lang['config_show_receipt_after_suspending_sale'] = 'Show receipt after suspending sale';
$lang['config_backup_overview'] = 'Backup Overview';
$lang['config_backup_overview_desc'] = 'Backing up your data is very important, but can be troublesome with large amount of data. If you have lots of images, items, and sales this can increase the size of your database.';
$lang['config_backup_options'] = 'We offer many options for backup to help you decide how to proceed';
$lang['config_backup_simple_option'] = 'Clicking "Backup database". This will attempt to download your whole database to a file. If you get a blank screen or can\'t download the file, try one of the other options.';
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin is a popular tool for managing your databases. If you are using the download version with installer, it can be accessed by going to';
$lang['config_backup_phpmyadmin_2'] = 'Your username is root and password is what you used during initial installation of PHP POS. Once logged in select your database from the panel on the left. Then select export and then submit the form.';
$lang['config_backup_control_panel'] = 'If you have installed on your own server that has a control panel such as cpanel, look for the backup module which will often let you download backups of your database.';
$lang['config_backup_mysqldump'] = 'If you have access to the shell and mysqldump on your server, you can try to execute it by clicking the below link. Otherwise  you will need to try other options.';
$lang['config_mysqldump_failed'] = 'mysqldump backup has failed. This could be due to a server restriction or the command might not be available. Please try another backup method';



$lang['config_looking_for_location_settings'] = 'Looking for other configuration options? Go to';
$lang['config_module'] = 'Module';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Calculate Average Cost Price from Receivings';
$lang['config_averaging_method'] = 'Averaging Method';
$lang['config_historical_average'] = 'Historical Average';
$lang['config_moving_average'] = 'Moving Average';

$lang['config_hide_dashboard_statistics'] = 'Hide Dashboard Statistics';
$lang['config_hide_store_account_payments_in_reports'] = 'Hide Store Account Payments In Reports';
$lang['config_id_to_show_on_sale_interface'] = 'Item ID to Show on Sales Interface';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Auto Focus On Item Field When using Sales/Receivings Interfaces';
$lang['config_automatically_show_comments_on_receipt'] = 'Automatically Show Comments on Receipt';
$lang['config_hide_customer_recent_sales'] = 'Hide Recent Sales for Customer';
$lang['config_spreadsheet_format'] = 'Spreadsheet Format';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Disable Giftcard Detection';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Disable giftcard subtraction when using giftcard during sale';
$lang['config_always_show_item_grid'] = 'Always Show Item Grid';
$lang['config_legacy_detailed_report_export'] = 'Legacy Detailed Report Excel Export';
$lang['config_print_after_receiving'] = 'Print receipt after receiving';
$lang['config_company_info'] = 'Company Information';


$lang['config_suspended_sales_layaways_info'] = 'Suspended Sales/Layaways';
$lang['config_application_settings_info'] = 'Application Settings';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Hide barcode on receipts';
$lang['config_round_tier_prices_to_2_decimals'] = 'Round tier Prices to 2 decimals';
$lang['config_group_all_taxes_on_receipt'] = 'Group all taxes on receipt';
$lang['config_receipt_text_size'] = 'Receipt text size';
$lang['config_small'] = 'Small';
$lang['config_medium'] = 'Medium';
$lang['config_large'] = 'Large';
$lang['config_extra_large'] = 'Extra large';
$lang['config_select_sales_person_during_sale'] = 'Select sales person during sale';
$lang['config_default_sales_person'] = 'Default sales person';
$lang['config_require_customer_for_sale'] = 'Require customer for sale';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Hide store account payments from report totals';
$lang['config_disable_sale_notifications'] = 'Disable sale notifications';
$lang['config_id_to_show_on_barcode'] = 'ID to show on barcode';
$lang['config_currency_denoms'] = 'Currency Denominations';
$lang['config_currency_value'] = 'Currency Value';
$lang['config_add_currency_denom'] = 'Add currency denomination';
$lang['config_enable_timeclock'] = 'Enable Time Clock';
$lang['config_change_sale_date_for_new_sale'] = 'Change Sale Date For New Sale';
$lang['config_dont_average_use_current_recv_price'] = 'Don\'t average, use current received price';
$lang['config_number_of_recent_sales'] = 'Number of recent sales by customer to show';
$lang['config_hide_suspended_recv_in_reports'] = 'Hide suspended Receivings in reports';
$lang['config_calculate_profit_for_giftcard_when'] = 'Calculate Gift Card Profit When';
$lang['config_selling_giftcard'] = 'Selling Gift Card';
$lang['config_redeeming_giftcard'] = 'Redeeming Gift Card';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Remove customer contact info from receipt';
$lang['config_speed_up_search_queries'] = 'Speed up search queries?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Redirect to sale or receiving screen after printing receipt';
$lang['config_enable_sounds'] = 'Enable sounds for status messages';
$lang['config_charge_tax_on_recv'] = 'Charge tax on receivings';
$lang['config_report_sort_order'] = 'Report Sort Order';
$lang['config_asc'] = 'Oldest first';
$lang['config_desc'] = 'Newest first';
$lang['config_do_not_group_same_items'] = 'Do NOT group items that are the same';
$lang['config_show_item_id_on_receipt'] = 'Show item id on receipt';
$lang['config_show_language_switcher'] = 'Show Language Switcher';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'Do not allow out of stock items to be sold';
$lang['config_number_of_items_in_grid'] = 'Number of items per page in grid';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Edit item price if 0 after adding to sale';
$lang['config_override_receipt_title'] = 'Override receipt title';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Automatically print duplicate receipt for credit card transactions';






$lang['config_default_type_for_grid'] = 'Default type for Grid';
$lang['config_billing_is_managed_through_paypal'] = 'Billing is managed through  <a target="_blank" href="http://paypal.com">Paypal</a>. You can cancel your subscription by clicking <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">here</a>. You can <a href="http://phppointofsale.com/update_billing.php" target="_blank">update billing here</a>.';
$lang['config_cannot_change_language'] = 'Language cannot be saved at application level. However the default admin employee can change the language using the selector in the header of the program';
$lang['disable_quick_complete_sale'] = 'Disable sale quick complete';
$lang['config_fast_user_switching'] = 'Enable fast user switching (password not required)';
$lang['config_require_employee_login_before_each_sale'] = 'Require employee login before each sale';
$lang['config_reset_location_when_switching_employee'] = 'Reset location when switching employee';
$lang['config_number_of_decimals'] = 'Number of decimals';
$lang['config_let_system_decide'] = 'Let system decide (Recommended)';
$lang['config_thousands_separator'] = 'Thousands Separator';
$lang['config_enhanced_search_method'] = 'Enhanced Search Method';
$lang['config_hide_store_account_balance_on_receipt'] = 'Hide store account balance on receipt';
$lang['config_decimal_point'] = 'Decimal Point';
$lang['config_hide_out_of_stock_grid'] = 'Hide out of stock items in grid';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Highlight low inventory items in items module';
$lang['config_sort'] = 'Sort';
$lang['config_enable_customer_loyalty_system'] = 'Enable Customer Loyalty system';
$lang['config_spend_to_point_ratio'] = 'Spend amount to point ratio';
$lang['config_point_value'] = 'Point Value';
$lang['config_hide_points_on_receipt'] = 'Hide Points On Receipt';
$lang['config_show_clock_on_header'] = 'Show Clock in Header';
$lang['config_show_clock_on_header_help_text'] = 'This is visible only on wide screens';
$lang['config_loyalty_explained_spend_amount'] = 'Enter the amount to spend';
$lang['config_loyalty_explained_points_to_earn'] = 'Enter points to be earned';
$lang['config_simple'] = 'Simple';
$lang['config_advanced'] = 'Advanced';
$lang['config_loyalty_option'] = 'Loyalty Program Option';
$lang['config_number_of_sales_for_discount'] = 'Number of sales for discount';
$lang['config_discount_percent_earned'] = 'Discount percent earned when reaching sales';
$lang['hide_sales_to_discount_on_receipt'] = 'Hide sales to discount on receipt';
$lang['config_hide_price_on_barcodes'] = 'Hide price on barcodes';
$lang['config_always_use_average_cost_method'] = 'Always Use Global Average Cost Price For A Sale Item\'s Cost Price. (DO NOT check unless you know what it means)';

$lang['config_test_mode_help'] = 'Sales NOT saved';
$lang['config_require_customer_for_suspended_sale'] = 'Require customer for suspended sale';
$lang['config_default_new_items_to_service'] = 'Default New Items as service items';






$lang['config_prompt_for_ccv_swipe'] = 'Prompt for CCV when swiping credit card';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Disable store account when over credit limit';
$lang['config_mailing_labels_type'] = 'Mailing Labels Format';
$lang['config_phppos_session_expiration'] = 'Session expiration';
$lang['config_hours'] = 'Hours';
$lang['config_never'] = 'Never';
$lang['config_on_browser_close'] = 'On Browser Close';
$lang['config_do_not_allow_below_cost'] = 'Do NOT allow items to be sold below cost price';
$lang['config_store_account_statement_message'] = 'Store Account Statement Message';
$lang['config_enable_margin_calculator'] = 'Enable Mark Up Calculator';
$lang['config_enable_quick_edit'] = 'Enable quick edit on manage pages';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Show original price on receipt if marked down';
$lang['config_cancel_account'] = 'Cancel Account';
$lang['config_update_billing'] = 'You can update and cancel your billing information by clicking the buttons below:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Include child categories when searching or reporting';
$lang['config_confirm_error_messages_modal'] = 'Confirm error messages using modal dialogs';
$lang['config_remove_commission_from_profit_in_reports'] = 'Remove commission from profit in reports';
$lang['config_remove_points_from_profit'] = 'Remove points redemption from profit';
$lang['config_capture_sig_for_all_payments'] = 'Capture signature for all sales';
$lang['config_suppliers_store_accounts'] = 'Suppliers Store Accounts';
$lang['config_currency_symbol_location'] = 'Currency Symbol Location';
$lang['config_before_number'] = 'Before Number';
$lang['config_after_number'] = 'After Number';
$lang['config_hide_desc_on_receipt'] = 'Hide Description on Receipt';
$lang['config_default_percent_off'] = 'Default Percent Off';
$lang['config_default_cost_plus_percent'] = 'Default Cost Plus Percent';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Default Tier Percent Type for excel import';
$lang['config_override_tier_name'] = 'Override Tier Name on Receipt';
$lang['config_loyalty_points_without_tax'] = 'Loyalty points earned not including tax';
$lang['config_lock_prices_suspended_sales'] = 'Lock prices when unsuspending sale even if they belong to a tier';
$lang['config_remove_customer_name_from_receipt'] = 'Remove Customer Name From Receipt';
$lang['config_scale_1'] = 'UPC-12 4 price digits';
$lang['config_scale_2'] = 'UPC-12 5 Price Digits';
$lang['config_scale_3'] = 'EAN-13 5 price digits';
$lang['config_scale_4'] = 'EAN-13 6 price digits';
$lang['config_scale_format'] = 'Scale Barcode Format';
;
$lang['config_enable_scale'] = 'Enable Scale';

$lang['config_woocommerce_settings_info'] = 'Woocommerce Settings';
$lang['config_store_location'] = 'Store Location';

$lang['config_woo_api_secret'] = 'Woocommerce API Secret';
$lang['config_woo_api_url'] = 'Woocommerce API Url';
$lang['config_woo_api_key'] = 'Woocommerce API Key';











$lang['config_ecommerce_settings_info'] = 'Ecommerce Platform';
$lang['config_ecommerce_platform'] = 'Select Platform';
$lang['config_magento_settings_info'] = 'Magento Settings';
$lang['config_scale_divide_by'] = 'Scale Price Divide By';
$lang['config_do_not_force_http'] = 'Do not force HTTP when needed for EMV Credit Card Processing';
$lang['config_logout_on_clock_out'] = 'Log out automatically when clocking out';
$lang['config_user_configured_layaway_name'] = 'Override Layaway Name';
$lang['config_virtual_keyboard'] = 'Virtual Keyboard (On/Off)';
$lang['config_use_tax_value_at_all_locations'] = 'Use Tax Values at ALL locations';
$lang['config_enable_ebt_payments'] = 'Enable EBT payments';
$lang['config_item_id_auto_increment'] = 'Item ID Auto Increment Starting Value';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'There was an error changing auto_increment for item_id';
$lang['config_item_kit_id_auto_increment'] = 'Item Kit ID Auto Increment Starting Value';
$lang['config_sale_id_auto_increment'] = 'Sale ID Auto Increment Starting Value';
$lang['config_receiving_id_auto_increment'] = 'Receiving ID Auto Increment Starting Value';
$lang['config_change_auto_increment_item_kit_id'] = 'There was an error changing auto_increment for  Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'There was an error changing auto_increment for sale_id';
$lang['config_change_auto_increment_receiving_id'] = 'There was an error changing auto_increment for receiving_id';
$lang['config_auto_increment_note'] = 'You can only increase Auto Increment values. Updating them will not affect IDs for items, item kits, sales or receivings that already exist.';

$lang['config_online_price_tier'] = 'Online Price Tier';
$lang['config_email_settings_info'] = 'Email Settings';

$lang['config_last_sync_date'] = 'Last Sync Date';
$lang['config_sync'] = 'Sync';
$lang['config_smtp_crypto'] = 'SMTP Encryption';
$lang['config_email_protocol'] = 'Mail Sending Protocol';
$lang['config_smtp_host'] = 'SMTP Server Address';
$lang['config_smtp_user'] = 'Email Address';
$lang['config_smtp_pass'] = 'Email Password';
$lang['config_smtp_port'] = 'SMTP Port';
$lang['config_email_charset'] = 'Character set';
$lang['config_email_newline'] = 'Newline character';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'SMTP Timeout';
$lang['config_send_test_email'] = 'Send Test Email';
$lang['config_please_enter_email_to_send_test_to'] = 'Please enter email address to send test email to';
$lang['config_email_succesfully_sent'] = 'Email has been sent successfully';
$lang['config_taxes_info'] = 'Taxes';
$lang['config_currency_info'] = 'Currency';

$lang['config_receipt_info'] = 'Receipt';

$lang['config_barcodes_info'] = 'Barcodes';
$lang['config_customer_loyalty_info'] = 'Customer Loyalty';
$lang['config_price_tiers_info'] = 'Price Tiers';
$lang['config_auto_increment_ids_info'] = 'ID Numbers';
$lang['config_items_info'] = 'Items';
$lang['config_employee_info'] = 'Employee';
$lang['config_store_accounts_info'] = 'Store Accounts';
$lang['config_sales_info'] = 'Sales';
$lang['config_payment_types_info'] = 'Payment Types';
$lang['config_profit_info'] = 'Profit Calculation';
$lang['reports_view_dashboard_stats'] = 'View Dashboard Statistics';
$lang['config_keyword_email'] = 'email settings';
$lang['config_keyword_company'] = 'company';
$lang['config_keyword_taxes'] = 'taxes';
$lang['config_keyword_currency'] = 'currency';
$lang['config_keyword_payment'] = 'payment';
$lang['config_keyword_sales'] = 'sales';
$lang['config_keyword_suspended_layaways'] = 'suspended layaways';
$lang['config_keyword_receipt'] = 'receipt';
$lang['config_keyword_profit'] = 'profit';
$lang['config_keyword_barcodes'] = 'barcodes';
$lang['config_keyword_customer_loyalty'] = 'customer loyalty';
$lang['config_keyword_price_tiers'] = 'price tiers';
$lang['config_keyword_auto_increment'] = 'starting auto increment id numbers database';
$lang['config_keyword_items'] = 'items';
$lang['config_keyword_employees'] = 'employees';
$lang['config_keyword_store_accounts'] = 'store accounts';
$lang['config_keyword_application_settings'] = 'application settings';
$lang['config_keyword_ecommerce'] = 'ecommerce platform';
$lang['config_keyword_woocommerce'] = 'woocommerce settings ecommerce';
$lang['config_billing_info'] = 'Billing Information';
$lang['config_keyword_billing'] = 'billing cancel update';
$lang['config_woo_version'] = 'WooCommerce Version';

$lang['sync_phppos_item_changes'] = 'Sync item changes';
$lang['config_sync_phppos_item_changes'] = 'Sync item changes';
$lang['config_import_ecommerce_items_into_phppos'] = 'Import items into phppos';
$lang['config_sync_inventory_changes'] = 'Sync inventory changes';
$lang['config_export_phppos_tags_to_ecommerce'] = 'Export tags to ecommerce';
$lang['config_export_phppos_categories_to_ecommerce'] = 'Export categories to ecommerce';
$lang['config_export_phppos_items_to_ecommerce'] = 'Export items to ecommerce';
$lang['config_ecommerce_cron_sync_operations'] = 'Ecommerce Sync Operations';
$lang['config_ecommerce_progress'] = 'Sync Progress';
$lang['confirmation_woocommerce_cron_cancel'] = 'Are you sure you want to cancel the sync?';
$lang['config_force_https'] = 'Require https for program';

$lang['config_keyword_price_rules'] = 'Price Rules';
$lang['config_disable_price_rules_dialog'] = 'Disable Price Rules dialog';
$lang['config_price_rules_info'] = 'Price Rules';

$lang['config_prompt_to_use_points'] = 'Prompt to use points when available';



$lang['config_always_print_duplicate_receipt_all'] = 'Always print duplicate receipt for all transactions';


$lang['config_orders_and_deliveries_info'] = 'Orders And Deliveries';
$lang['config_delivery_methods'] = 'Delivery Methods';
$lang['config_shipping_providers'] = 'Shipping Providers';
$lang['config_expand'] = 'Expand';
$lang['config_add_delivery_rate'] = 'Add Delivery Rate';
$lang['config_add_shipping_provider'] = 'Add Shipping Provider';
$lang['config_delivery_rates'] = 'Delivery Rates';
$lang['config_delivery_fee'] = 'Delivery Fee';
$lang['config_keyword_orders_deliveries'] = 'orders delivery deliveries';
$lang['config_delivery_fee_tax'] = 'Delivery Fee Tax';
$lang['config_add_rate'] = 'Add Rate';
$lang['config_delivery_time'] = 'Delivery Time In Days';
$lang['config_delivery_rate'] = 'Delivery Rate';
$lang['config_rate_name'] = 'Rate Name';
$lang['config_rate_fee'] = 'Rate Fee';
$lang['config_rate_tax'] = 'Rate Tax';
$lang['config_tax_classes'] = 'Tax Groups';
$lang['config_add_tax_class'] = 'Add Tax Group';

$lang['config_wide_printer_receipt_format'] = 'Wide Printer Receipt Format';

$lang['config_default_cost_plus_fixed_amount'] = 'Default Cost Plus Fixed Amount';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Default Tier Fixed Amount for Excel Import';
$lang['config_default_reorder_level_when_creating_items'] = 'Default Reorder Level When Creating Items';
$lang['config_remove_customer_company_from_receipt'] = 'Remove customer company name from receipt';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Import categories into phppos';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Imports tags into phppos';

$lang['config_shipping_zones'] = 'Shipping Zones';
$lang['config_add_shipping_zone'] = 'Add Shipping Zone';
$lang['config_no_results'] = 'No Results';
$lang['config_zip_search_term'] = 'Type in a zipcode';
$lang['config_searching'] = 'Searching...';
$lang['config_tax_class'] = 'Tax Group';
$lang['config_zone'] = 'Zone';

$lang['config_zip_codes'] = 'Zip Codes';
$lang['config_add_zip_code'] = 'Add Zip Code';
$lang['config_ecom_sync_logs'] = 'E-Commerce Syncing Logs';
$lang['config_currency_code'] = 'Currency Code';

$lang['config_add_currency_exchange_rate'] = 'Add Currency Exchange Rate';
$lang['config_currency_exchange_rates'] = 'Exchange Rates';
$lang['config_exchange_rate'] = 'Exchange Rate';
$lang['config_item_lookup_order'] = 'Item Lookup Order';
$lang['config_item_id'] = 'Item Id';
$lang['config_reset_ecommerce'] = 'Reset E-Commerce';
$lang['config_confirm_reset_ecom'] = 'Are you sure you want to reset e-commerce? This will only reset php point of sale so items are no longer linked';
$lang['config_reset_ecom_successfully'] = 'You have reset E-Commerce successfully';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Number of Decimals for Quantity On Receipt';
$lang['config_enable_wic'] = 'Enable WIC';
$lang['config_store_opening_time'] = 'Store Opening Time';
$lang['config_store_closing_time'] = 'Store Closing Time';
$lang['config_limit_manual_price_adj'] = 'Limit Manual Price Adjustments And Discounts';
$lang['config_always_minimize_menu'] = 'Always Minimize Left Side Bar Menu';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'Do NOT tax service items for deliveries';
$lang['config_paypal_me'] = 'PayPal.me Username';
?>