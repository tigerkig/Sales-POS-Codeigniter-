<?php
$lang['config_info']='Información de la configuración de la tienda';

$lang['config_address']='Dirección de la compañía';
$lang['config_phone']='Teléfono de la compañía';
$lang['config_website']='Sitio web';
$lang['config_fax']='Fax';
$lang['config_default_tax_rate']='% de la tasa de impuestos predeterminado';


$lang['config_company_required']='Es necesario ingresar el nombre de la compañía';

$lang['config_phone_required']='Es necesario ingresar el teléfono de la compañía';
$lang['config_default_tax_rate_required']='Es necesario ingresar el porcentaje de la tasa de impuestos';
$lang['config_default_tax_rate_number']='La tasa de impuestos predeterminada debe contener un valor numérico';
$lang['config_company_website_url']='El sitio web que ingresó no mantiene un formato estándar (http://...)';

$lang['config_saved_unsuccessfully']='Error al guardar la configuración. No se permite generar cambios en el modo de demostración o los impuestos no fueron guardados de manera exitosa ';
$lang['config_return_policy_required']='Es necesario ingresar la política de devoluciones';
$lang['config_print_after_sale']='¿Desea imprimir el recibo después de una venta?';


$lang['config_currency_symbol'] = 'Símbolo de moneda';
$lang['config_backup_database'] = 'Copia de seguridad de base de datos';
$lang['config_restore_database'] = 'Restaurar base de datos';

$lang['config_number_of_items_per_page'] = 'Número de artículos por página';
$lang['config_date_format'] = 'Formato de fecha';
$lang['config_time_format'] = 'Formato de hora';
$lang['config_company_logo'] = 'Logotipo de la compañía';
$lang['config_delete_logo'] = 'Eliminar logotipo';

$lang['config_optimize_database'] = 'Optimizar base de datos';
$lang['config_database_optimize_successfully'] = 'La base de datos ha sido optimizada de manera exitosa';
$lang['config_payment_types'] = 'Método de pago';
$lang['select_sql_file'] = 'Seleccione archivo SQL';
$lang['restore_heading'] = 'Esto le permitirá restaurar la base de datos';

$lang['type_file'] = 'Seleccione el archivo SQL desde su dispositivo';

$lang['restore'] = 'Restaurar';

$lang['required_sql_file'] = 'No se ha seleccionado el archivo SQL';

$lang['restore_db_success'] = 'La base de datos se ha restaurado de manera exitosa';

$lang['db_first_alert'] = '¿Está seguro que desea restaurar la base de datos?';
$lang['db_second_alert'] = 'Los datos existentes se eliminarán. ¿Desea continuar de cualquier manera?';
$lang['password_error'] = 'La contraseña no es válida';
$lang['password_required'] = 'Es necesario ingresar la constraseña';
$lang['restore_database_title'] = 'Restaurar base de datos';
$lang['config_use_scale_barcode'] = 'Usar códigos de barra a escala';

$lang['config_environment'] = 'Entorno';


$lang['config_sandbox'] = 'Área de pruebas';
$lang['config_production'] = 'Producción';
$lang['disable_confirmation_sale']='¿Desea desactivar la confirmación de venta completada?';



$lang['config_default_payment_type'] = 'Método de pago estándar';
$lang['config_speed_up_note'] = 'Solo se recomienda si usted tiene más de 10.000 artículos o clientes';
$lang['config_hide_signature'] = '¿Ocultar firma?';
$lang['config_automatically_email_receipt']='Envío automático al correo electrónico del cliente';
$lang['config_barcode_price_include_tax']='Incluyen el impuesto sobre los códigos de barras';
$lang['config_round_cash_on_sales'] = 'Redondear a la cifra más cercana en .05 (Sólo para Canadá)';

$lang['config_prefix'] = 'Prefijo del Id. de Venta';
$lang['config_sale_prefix_required'] = 'Es necesario introducir un prefijo Id. de Venta';
$lang['config_customers_store_accounts'] = 'Permitir cuentas de crédito dentro de la tienda';
$lang['config_change_sale_date_when_suspending'] = 'Cambiar la fecha de venta cuando se suspende una venta';
$lang['config_change_sale_date_when_completing_suspended_sale'] = 'Cambiar la fecha de venta al completar una venta suspendida';
$lang['config_price_tiers'] = 'Niveles de precios';
$lang['config_add_tier'] = 'Agregar nivel';
$lang['config_show_receipt_after_suspending_sale'] = 'Mostrar recibo después de la suspensión de una venta';
$lang['config_backup_overview'] = 'Información general de la copia de seguridad';
$lang['config_backup_overview_desc'] = 'Realizar copias de seguridad de sus datos es muy importante, pero puede ser un problema con una gran cantidad de datos. Si usted tiene una gran variedad de imágenes, artículos y ventas, esto podría aumentar significativamente el tamaño de su base de datos.';
$lang['config_backup_options'] = 'Contámos con varias opciones para crear respaldos de seguridad para que así usted pueda decidir entre las mejores opciones';
$lang['config_backup_simple_option'] = 'Al hacer clic en &quot;Respaldo de la base de datos&quot;. Este intentará descargar toda su base de datos a un archivo. Si se muestra una pantalla en blanco o no puede descargar el archivo, pruebe con una de las diferentes opciones.';
$lang['config_backup_phpmyadmin_1'] = 'PhpMyAdmin es una herramienta popular para la gestión de bases de datos. Si está utilizando la versión de descarga con el instalador, se puede acceder dirigiendose hacía';
$lang['config_backup_phpmyadmin_2'] = 'Su nombre de usuario es root y la contraseña es la que se utilizó durante la instalación inicial de PHP POS. Una vez que se ha conectado, seleccione la base de datos desde el panel de la izquierda. A continuación, seleccione la exportación y luego envíe el formulario.';
$lang['config_backup_control_panel'] = 'Si ha instalado en su propio servidor que cuenta con un panel de control como Cpanel, busque el módulo de copias de seguridad que a menudo le permitirá descargar copias de seguridad de su base de datos.';
$lang['config_backup_mysqldump'] = 'Si usted tiene acceso al Shell y a Mysqldump en su servidor, usted puede tratar de ejecutarlo haciendo clic en el enlace de abajo. De lo contrario, tendrá que intentar con otras opciones disponibles.';
$lang['config_mysqldump_failed'] = 'El proceso de respaldo de Mysqldump ha fallado. Esto podría ser debido a una restricción en el servidor o en el comando que podría no estar disponible. Por favor intente otro método de copia de seguridad';



$lang['config_looking_for_location_settings'] = '¿Está buscando otras opciones de configuración? Diríjase a';
$lang['config_module'] = 'Módulo';
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = 'Calcular el costo promedio de la compra';
$lang['config_averaging_method'] = 'Método de promedio';
$lang['config_historical_average'] = 'Promedio histórico';
$lang['config_moving_average'] = 'Promedio móvil';

$lang['config_hide_dashboard_statistics'] = 'Ocultar el panel de estadísticas';
$lang['config_hide_store_account_payments_in_reports'] = 'Ocultar las cuentas por pagar en los reportes de la tienda';
$lang['config_id_to_show_on_sale_interface'] = 'Columna para mostrar en la interfaz de ventas';
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = 'Posicionar el cursor en el campo del artículo en la interfaz de ventas y entradas';
$lang['config_automatically_show_comments_on_receipt'] = 'Mostrar automáticamente observaciones sobre un recibo';
$lang['config_hide_customer_recent_sales'] = 'Ocultar ventas recientes para el cliente';
$lang['config_spreadsheet_format'] = 'Formato de la hoja de cálculo';
$lang['config_csv'] = 'CSV';
$lang['config_xlsx'] = 'XLSX';
$lang['config_disable_giftcard_detection'] = 'Desactivar la detección de tarjetas de regalo';
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = 'Deshabilitar la substracción de dinero de una tarjeta de regalo mientras se realiza una venta';
$lang['config_always_show_item_grid'] = 'Mostrar siempre la cuadrícula de artículos';
$lang['config_legacy_detailed_report_export'] = 'Exportación de un reporte detallado de activos a un archivo de Excel';
$lang['config_print_after_receiving'] = 'Imprimir recibo después de que se ha generado una entrada';
$lang['config_company_info'] = 'Información de la compañía';


$lang['config_suspended_sales_layaways_info'] = 'Ventas suspendidas / Sistema de apartado';
$lang['config_application_settings_info'] = 'Configuración de la aplicación';
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = 'Ocultar el código de barras dentro de un recibo';
$lang['config_round_tier_prices_to_2_decimals'] = 'Redondear nivel de precios a dos decimales';
$lang['config_group_all_taxes_on_receipt'] = 'Agrupar todos los impuestos en el recibo';
$lang['config_receipt_text_size'] = 'Tamaño del texto en el recibo';
$lang['config_small'] = 'Pequeño';
$lang['config_medium'] = 'Medio';
$lang['config_large'] = 'Grande';
$lang['config_extra_large'] = 'Extra grande';
$lang['config_select_sales_person_during_sale'] = 'Seleccionar al vendedor mientras se realiza la venta';
$lang['config_default_sales_person'] = 'Vendedor por defecto';
$lang['config_require_customer_for_sale'] = 'Hacer necesario ingresar a un cliente para proceder con la venta';

$lang['config_hide_store_account_payments_from_report_totals'] = 'Ocultar pagos a la cuenta de la tienda desde el Reporte de totales';
$lang['config_disable_sale_notifications'] = 'Desactivar notificaciones dentro de una venta';
$lang['config_id_to_show_on_barcode'] = 'Id. para mostrar en el código de barras';
$lang['config_currency_denoms'] = 'Denominaciones de moneda';
$lang['config_currency_value'] = 'Valor de la moneda';
$lang['config_add_currency_denom'] = 'Añadir denominación de la moneda';
$lang['config_enable_timeclock'] = 'Activar reloj checador';
$lang['config_change_sale_date_for_new_sale'] = 'Cambiar la fecha de venta para una venta nueva';
$lang['config_dont_average_use_current_recv_price'] = 'Sin promedio, utilice el precio actual recibido';
$lang['config_number_of_recent_sales'] = 'Número de ventas recientes por cliente para mostrar';
$lang['config_hide_suspended_recv_in_reports'] = 'Ocultar entradas suspendidas en los informes';
$lang['config_calculate_profit_for_giftcard_when'] = 'Calcular la ganancia de la tarjeta de regalo cuando';
$lang['config_selling_giftcard'] = 'Vendiendo tarjeta de regalo';
$lang['config_redeeming_giftcard'] = 'Redimiendo tarjeta de regalo';
$lang['config_remove_customer_contact_info_from_receipt'] = 'Eliminar información de contacto del cliente del recibo';
$lang['config_speed_up_search_queries'] = '¿Agilizar las consultas de búsqueda?';




$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = 'Redirigir a la pantalla de ventas o entradas después de imprimir al recibo';
$lang['config_enable_sounds'] = 'Activar sonidos para mensajes de estado';
$lang['config_charge_tax_on_recv'] = 'Cargar impuesto en entradas';
$lang['config_report_sort_order'] = 'Ordenar por tipo de reporte';
$lang['config_asc'] = 'Primero lo antiguo';
$lang['config_desc'] = 'Primero lo nuevo';
$lang['config_do_not_group_same_items'] = 'No agrupar los artículos que son iguales';
$lang['config_show_item_id_on_receipt'] = 'Mostrar Id. del artículo en el recibo';
$lang['config_show_language_switcher'] = 'Mostrar la selección de lenguajes';
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = 'No permitir la venta de productos sin existencias.';
$lang['config_number_of_items_in_grid'] = 'Número de artículos por la página en la cuadrícula';
$lang['config_edit_item_price_if_zero_after_adding'] = 'Editar el precio del artículo si es igual a 0 después de añadir a la venta';
$lang['config_override_receipt_title'] = 'Anular el título del recibo';
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = 'Imprima automáticamente un recibo duplicado para las transacciones con tarjeta de crédito';






$lang['config_default_type_for_grid'] = 'Tipo predeterminado para la cuadrícula';
$lang['config_billing_is_managed_through_paypal'] = 'La facturación se gestiona a través de <a target="_blank" href="http://paypal.com">Paypal</a>. Usted puede cancelar su suscripción haciendo clic <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">aquí</a>. <a href="http://phppointofsale.com/update_billing.php" target="_blank">Puede actualizar la facturación aquí</a>';
$lang['config_cannot_change_language'] = 'El idioma no se puede guardar en el nivel de aplicación. Sin embargo, el empleado de administración predeterminado puede cambiar el idioma utilizando el selector en el encabezado del programa';
$lang['disable_quick_complete_sale'] = 'Desactivar la función de venta rápida';
$lang['config_fast_user_switching'] = 'Activar cambio rápido de usuario (la contraseña no es obligatoria)';
$lang['config_require_employee_login_before_each_sale'] = 'Exigir a los empleados iniciar sesión antes de cada venta';
$lang['config_keep_same_location_after_switching_employee'] = 'Conserve la misma ubicación después de cambiar de empleado';
$lang['config_number_of_decimals'] = 'Número de decimales';
$lang['config_let_system_decide'] = 'Dejar que el sistema tome la mejor decisión (recomendado)';
$lang['config_thousands_separator'] = 'Separador de miles';
$lang['config_enhanced_search_method'] = 'Método de búsqueda mejorado';
$lang['config_hide_store_account_balance_on_receipt'] = 'Ocultar el saldo de la cuenta de la tienda en el recibo';
$lang['config_decimal_point'] = 'Punto decimal';
$lang['config_hide_out_of_stock_grid'] = 'Ocultar cuadrícula con artículos fuera de inventario.';
$lang['config_highlight_low_inventory_items_in_items_module'] = 'Resaltar elementos con un bajo inventario en el módulo de artículos';
$lang['config_sort'] = 'Tipo';
$lang['config_enable_customer_loyalty_system'] = 'Activar el sistema de lealtad del cliente';
$lang['config_spend_to_point_ratio'] = 'Señalar la proporción de la cantidad de gasto';
$lang['config_point_value'] = 'Valor del punto';
$lang['config_hide_points_on_receipt'] = 'Ocultar puntos en el recibo';
$lang['config_show_clock_on_header'] = 'Mostrar el reloj en el encabezado';
$lang['config_show_clock_on_header_help_text'] = 'Esto solo se visualizara en pantallas amplias';
$lang['config_loyalty_explained_spend_amount'] = 'Ingrese la cantidad de gasto';
$lang['config_loyalty_explained_points_to_earn'] = 'Introduzca los puntos obtenidos';
$lang['config_simple'] = 'Sencillo';
$lang['config_advanced'] = 'Avanzado';
$lang['config_loyalty_option'] = 'Opcion del programa de lealtad';
$lang['config_number_of_sales_for_discount'] = 'Número de ventas para alcanzar un descuento';
$lang['config_discount_percent_earned'] = 'Porcentaje de descuento alcanzado por las ventas generadas';
$lang['hide_sales_to_discount_on_receipt'] = 'Ocultar ventas necesarias en el recibo para obtener un descuento';
$lang['config_hide_price_on_barcodes'] = 'Ocultar precio en los códigos de barras';
$lang['config_always_use_average_cost_method'] = 'Usar siempre Global Media Cost para la venta de un ítem del precio de costo. (NO marque menos que sepa lo que significa)';

$lang['config_test_mode_help'] = 'En el modo de prueba las ventas NO se guardan';
$lang['config_require_customer_for_suspended_sale'] = 'Requerir al cliente para una venta suspendida';
$lang['config_default_new_items_to_service'] = 'Hacer por defecto los nuevos artículos como artículos de servicio';






$lang['config_prompt_for_ccv_swipe'] = 'Preguntar por el CCV al pasar la tarjeta de crédito';
$lang['config_disable_store_account_when_over_credit_limit'] = 'Desactivar la cuenta de tienda cuando se supere el límite de crédito';
$lang['config_mailing_labels_type'] = 'Formato de las etiquetas de correo';
$lang['config_phppos_session_expiration'] = 'Expiración de la sesión';
$lang['config_hours'] = 'Horas';
$lang['config_never'] = 'Nunca';
$lang['config_on_browser_close'] = 'Cerrar navegador';
$lang['config_do_not_allow_below_cost'] = 'NO permitir que los artículos se vendan por debajo de su precio de coste';
$lang['config_store_account_statement_message'] = 'Mensaje del estado de cuenta de la tienda';
$lang['config_enable_margin_calculator'] = 'Activar la marca encima de la calculadora';
$lang['config_disable_quick_edit'] = 'Desactivar la edición rápida en administrar páginas';
$lang['config_show_orig_price_if_marked_down_on_receipt'] = 'Mostrar el precio original en la recepción si está marcado abajo';
$lang['config_cancel_account'] = 'Cancelar cuenta';
$lang['config_update_billing'] = 'Puede actualizar y cancelar sus datos de facturación haciendo clic en los botones de abajo:';
$lang['config_include_child_categories_when_searching_or_reporting'] = 'Incluir categorías secundarias en la búsqueda o presentación de informes';
$lang['config_reset_location_when_switching_employee'] = 'Restablecer ubicación cuando se cambia empleado';
$lang['config_enable_quick_edit'] = 'Habilitar edición rápida de administrar páginas';
$lang['config_confirm_error_messages_modal'] = 'Confirmar mensajes de error utilizando cuadros de diálogo modales';
$lang['config_remove_commission_from_profit_in_reports'] = 'Retire la comisión de la ganancia en los informes';
$lang['config_remove_points_from_profit'] = 'Eliminar los puntos de redención de beneficios';
$lang['config_capture_sig_for_all_payments'] = 'captura de firmas para todas las ventas';
$lang['config_suppliers_store_accounts'] = 'Proveedores cuentas de tiendas';
$lang['config_currency_symbol_location'] = 'Símbolo monetario Localización';
$lang['config_before_number'] = 'antes de Número';
$lang['config_after_number'] = 'después de Número';
$lang['config_hide_desc_on_receipt'] = 'Hide Descripción de Recibo';
$lang['config_default_percent_off'] = 'Predeterminado por ciento de descuento';
$lang['config_default_cost_plus_percent'] = 'Costo por defecto Plus Porcentaje';
$lang['config_default_tier_percent_type_for_excel_import'] = 'Por defecto del Nivel Tipo ciento para la importación de Excel';
$lang['config_override_tier_name'] = 'Nivel anular Nombre de Recibo';
$lang['config_loyalty_points_without_tax'] = 'puntos de fidelidad ganados sin incluir el impuesto';
$lang['config_lock_prices_suspended_sales'] = 'los precios de bloqueo cuando Reactivando la venta, incluso si pertenecen a un nivel';
$lang['config_remove_customer_name_from_receipt'] = 'Retire Nombre del cliente desde la recepción';
$lang['config_scale_1'] = 'UPC-12 de 4 dígitos de precios';
$lang['config_scale_2'] = 'UPC-12 5 dígitos Precio';
$lang['config_scale_3'] = 'EAN-13 5 dígitos de precios';
$lang['config_scale_4'] = 'EAN-13 6 dígitos de precios';
$lang['config_scale_format'] = 'Formato Escala de código de barras';
;
$lang['config_enable_scale'] = 'Habilitar Escala';
$lang['config_scale_divide_by'] = 'Escala Divide Precio Por';
$lang['config_do_not_force_http'] = 'No fuerce HTTP cuando sea necesario para la EMV de procesamiento de tarjeta de crédito';
$lang['config_logout_on_clock_out'] = 'Cerrar la sesión automáticamente al fichar a cabo';
$lang['config_user_configured_layaway_name'] = 'Nombre anular Pago a plazos';
$lang['config_virtual_keyboard'] = 'Teclado Virtual (On / Off)';
$lang['config_use_tax_value_at_all_locations'] = 'Utilice los valores de impuesto en todos los lugares';
$lang['config_enable_ebt_payments'] = 'Permitir los pagos EBT';
$lang['config_item_id_auto_increment'] = 'Identificación de artículo incremento automático Valor partir';
$lang['config_change_auto_increment_item_id_unsuccessful'] = 'Se ha producido un error al cambiar AUTO_INCREMENT para item_id';
$lang['config_item_kit_id_auto_increment'] = 'Kit elemento de identificación automática Incremento Valor partir';
$lang['config_sale_id_auto_increment'] = 'Venta ID incremento automático Valor partir';
$lang['config_receiving_id_auto_increment'] = 'A partir de recibir Valor ID incremento automático';
$lang['config_change_auto_increment_item_kit_id'] = 'Se ha producido un error al cambiar AUTO_INCREMENT para Iitem_kit_id';
$lang['config_change_auto_increment_sale_id'] = 'Se ha producido un error al cambiar AUTO_INCREMENT para sale_id';
$lang['config_change_auto_increment_receiving_id'] = 'Se ha producido un error al cambiar AUTO_INCREMENT para receiving_id';
$lang['config_auto_increment_note'] = 'Sólo se pueden aumentar los valores de incremento automático. Su actualización no afectará a los ID de objetos, kits de artículos, ventas o receivings que ya existen.';
$lang['config_woo_api_key'] = 'WooCommerce clave de API';
$lang['config_email_settings_info'] = 'Ajustes del correo electrónico';
$lang['config_last_sync_date'] = 'Fecha última sincronización';
$lang['config_sync'] = 'Sincronizar';
$lang['config_online_price_tier'] = 'Precio en línea Tier';
$lang['config_smtp_crypto'] = 'El cifrado SMTP';
$lang['config_email_protocol'] = 'Protocolo El envío de correo';
$lang['config_smtp_host'] = 'Dirección del servidor SMTP';
$lang['config_smtp_user'] = 'Dirección de correo electrónico';
$lang['config_smtp_pass'] = 'Contraseña de Email';
$lang['config_smtp_port'] = 'Puerto SMTP';
$lang['config_email_charset'] = 'Conjunto de caracteres';
$lang['config_email_newline'] = 'carácter de nueva línea';
$lang['config_email_crlf'] = 'CRLF';
$lang['config_smtp_timeout'] = 'Tiempo de espera SMTP';
$lang['config_send_test_email'] = 'Enviar correo electrónico de prueba';
$lang['config_please_enter_email_to_send_test_to'] = 'Por favor, introduzca la dirección de correo electrónico para enviar correo electrónico de prueba para';
$lang['config_email_succesfully_sent'] = 'El correo electrónico ha sido enviado con éxito';
$lang['config_taxes_info'] = 'Impuestos';
$lang['config_currency_info'] = 'Moneda';

$lang['config_receipt_info'] = 'Recibo';

$lang['config_barcodes_info'] = 'Los códigos de barras';
$lang['config_customer_loyalty_info'] = 'La lealtad del cliente';
$lang['config_price_tiers_info'] = 'Niveles de precios';
$lang['config_auto_increment_ids_info'] = 'números de ID';
$lang['config_items_info'] = 'Artículos';
$lang['config_employee_info'] = 'Empleado';
$lang['config_store_accounts_info'] = 'Cuentas de las tiendas';
$lang['config_sales_info'] = 'Ventas';
$lang['config_payment_types_info'] = 'Formas de pago';
$lang['config_profit_info'] = 'Cálculo de ganancia';
$lang['reports_view_dashboard_stats'] = 'Ver del panel de estadísticas';
$lang['config_keyword_email'] = 'Ajustes del correo electrónico';
$lang['config_keyword_company'] = 'empresa';
$lang['config_keyword_taxes'] = 'impuestos';
$lang['config_keyword_currency'] = 'moneda';
$lang['config_keyword_payment'] = 'pago';
$lang['config_keyword_sales'] = 'ventas';
$lang['config_keyword_suspended_layaways'] = 'layaways suspendidos';
$lang['config_keyword_receipt'] = 'recibo';
$lang['config_keyword_profit'] = 'lucro';
$lang['config_keyword_barcodes'] = 'códigos de barras';
$lang['config_keyword_customer_loyalty'] = 'la lealtad del cliente';
$lang['config_keyword_price_tiers'] = 'niveles de precios';
$lang['config_keyword_auto_increment'] = 'a partir de bases de datos de incremento automático números de identificación';
$lang['config_keyword_items'] = 'artículos';
$lang['config_keyword_employees'] = 'empleados';
$lang['config_keyword_store_accounts'] = 'cuentas de tiendas';
$lang['config_keyword_application_settings'] = 'Configuraciones de la aplicación';
$lang['config_keyword_ecommerce'] = 'plataforma de comercio electrónico';
$lang['config_keyword_woocommerce'] = 'WooCommerce configuración de comercio electrónico';
$lang['config_billing_info'] = 'Datos de facturación';
$lang['config_keyword_billing'] = 'cancelar la actualización de facturación';
$lang['config_woo_version'] = 'Versión WooCommerce';

$lang['sync_phppos_item_changes'] = 'cambios elemento de sincronización';
$lang['config_sync_phppos_item_changes'] = 'cambios elemento de sincronización';
$lang['config_import_ecommerce_items_into_phppos'] = 'Importar elementos en phppos';
$lang['config_sync_inventory_changes'] = 'cambios en el inventario de sincronización';
$lang['config_export_phppos_tags_to_ecommerce'] = 'etiquetas de exportación a comercio electrónico';
$lang['config_export_phppos_categories_to_ecommerce'] = 'categorías de exportación a comercio electrónico';
$lang['config_export_phppos_items_to_ecommerce'] = 'artículos de exportación a comercio electrónico';
$lang['config_ecommerce_cron_sync_operations'] = 'Las operaciones de sincronización de comercio electrónico';
$lang['config_ecommerce_progress'] = 'El progreso de sincronización';
$lang['config_woocommerce_settings_info'] = 'Ajustes WooCommerce';
$lang['config_store_location'] = 'Ubicación de la tienda';
$lang['config_woo_api_secret'] = 'WooCommerce API Secreto';
$lang['config_woo_api_url'] = 'WooCommerce API Url';
$lang['config_ecommerce_settings_info'] = 'Plataforma de comercio electrónico';
$lang['config_ecommerce_platform'] = 'Seleccionar plataforma';
$lang['config_magento_settings_info'] = 'Ajustes de Magento';
$lang['confirmation_woocommerce_cron_cancel'] = '¿Está seguro de que desea cancelar la sincronización?';
$lang['config_force_https'] = 'Requerir HTTPS para el programa de';

$lang['config_keyword_price_rules'] = 'Reglas de precios';
$lang['config_disable_price_rules_dialog'] = 'Desactivar diálogo Reglas Precio';
$lang['config_price_rules_info'] = 'Reglas de precios';

$lang['config_prompt_to_use_points'] = 'Solicitud de uso puntos cuando esté disponible';



$lang['config_always_print_duplicate_receipt_all'] = 'Siempre imprima un recibo duplicado para todas las transacciones';


$lang['config_orders_and_deliveries_info'] = 'Pedidos y entregas';
$lang['config_delivery_methods'] = 'Métodos de entrega';
$lang['config_shipping_providers'] = 'Proveedores de envío';
$lang['config_expand'] = 'Expandir';
$lang['config_add_delivery_rate'] = 'Añadir tasa de entrega';
$lang['config_add_shipping_provider'] = 'Agregar proveedor de envío';
$lang['config_delivery_rates'] = 'Tarifas de Entrega';
$lang['config_delivery_fee'] = 'Gastos de envío';
$lang['config_keyword_orders_deliveries'] = 'Ordena las entregas';
$lang['config_delivery_fee_tax'] = 'Impuesto a la Entrega';
$lang['config_add_rate'] = 'Añadir tarifa';
$lang['config_delivery_time'] = 'Tiempo de entrega en días';
$lang['config_delivery_rate'] = 'Cargo de entrega';
$lang['config_rate_name'] = 'Nombre de la tarifa';
$lang['config_rate_fee'] = 'Tarifa de tarifa';
$lang['config_rate_tax'] = 'Tasa de Impuestos';
$lang['config_tax_classes'] = 'Grupos de impuestos';
$lang['config_add_tax_class'] = 'Agregar grupo de impuestos';

$lang['config_wide_printer_receipt_format'] = 'Formato de recepción de impresora';

$lang['config_default_cost_plus_fixed_amount'] = 'Costo por defecto más cantidad fija';
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Monto fijo de nivel predeterminado para importación de Excel';
$lang['config_default_reorder_level_when_creating_items'] = 'Nivel de reorden predeterminado al crear elementos';
$lang['config_remove_customer_company_from_receipt'] = 'Eliminar el nombre de la empresa del cliente del recibo';

$lang['config_import_ecommerce_categories_into_phppos'] = 'Importar categorías en phppos';
$lang['config_import_ecommerce_tags_into_phppos'] = 'Importa etiquetas en phppos';

$lang['config_shipping_zones'] = 'Zonas de envío';
$lang['config_add_shipping_zone'] = 'Añadir zona de envío';
$lang['config_no_results'] = 'No hay resultados';
$lang['config_zip_search_term'] = 'Escriba un código postal';
$lang['config_searching'] = 'Buscando...';
$lang['config_tax_class'] = 'Grupo de impuestos';
$lang['config_zone'] = 'Zona';

$lang['config_zip_codes'] = 'Códigos ZIP';
$lang['config_add_zip_code'] = 'Añadir código postal';
$lang['config_ecom_sync_logs'] = 'Registros de sincronización de comercio electrónico';
$lang['config_currency_code'] = 'Código de moneda';

$lang['config_add_currency_exchange_rate'] = 'Añadir Tipo de cambio de moneda';
$lang['config_currency_exchange_rates'] = 'Los tipos de cambio';
$lang['config_exchange_rate'] = 'Tipo de cambio';
$lang['config_item_lookup_order'] = 'Orden de búsqueda de artículos';
$lang['config_item_id'] = 'Identificación del artículo';
$lang['config_reset_ecommerce'] = 'Restablecer comercio electrónico';
$lang['config_confirm_reset_ecom'] = '¿Está seguro de que desea restablecer el comercio electrónico? Esto solo restablecerá el punto de venta de php para que los elementos ya no estén vinculados';
$lang['config_reset_ecom_successfully'] = 'Ha restablecido el comercio electrónico correctamente';
$lang['config_number_of_decimals_for_quantity_on_receipt'] = 'Número de decimales para la cantidad al recibo';
$lang['config_enable_wic'] = 'Habilitar WIC';
$lang['config_store_opening_time'] = 'Hora de apertura de la tienda';
$lang['config_store_closing_time'] = 'Tiempo de cierre de la tienda';
$lang['config_limit_manual_price_adj'] = 'Limitar ajustes manuales de precios y descuentos';
$lang['config_always_minimize_menu'] = 'Minimizar siempre el menú de la barra lateral izquierda';
$lang['config_do_not_tax_service_items_for_deliveries'] = 'NO cobrar impuestos por las entregas';
$lang['config_paypal_me'] = 'Nombre de usuario de PayPal.me';
?>