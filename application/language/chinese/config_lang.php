<?php
//Store Configuration Information
$lang['config_info'] = '存储配置信息';
//Company Address
$lang['config_address'] = '公司地址';
//Company Phone
$lang['config_phone'] = '公司电话';
//Sale ID Prefix
$lang['config_prefix'] = '销售ID前缀';
//Fax
$lang['config_fax'] = '传真';
//Default Tax Rate %
$lang['config_default_tax_rate'] = '默认税率％';
//Company name is a required field
$lang['config_company_required'] = '公司名称是必填字段';
//Company phone is a required field
$lang['config_phone_required'] = '公司电话是必填字段';
//Sale ID prefix is a required field
$lang['config_sale_prefix_required'] = '销售ID前缀是必填字段';
//The default tax rate is a required field
$lang['config_default_tax_rate_required'] = '默认税率是必填字段';
//The default tax rate must be a number
$lang['config_default_tax_rate_number'] = '默认税率必须是一个数字';
//Company website is not a valid URL (http://...)
$lang['config_company_website_url'] = '公司网站不是有效的网址（http：// ...）';
//Failed to save configuration. Configuration changes are not allowed in demo mode or taxes weren't saved correctly
$lang['config_saved_unsuccessfully'] = '无法保存配置。在演示模式下不允许进行配置更改，或者不能正确保存税金';
//Return policy is a required field
$lang['config_return_policy_required'] = '退货政策是必填字段';
//Print receipt after sale
$lang['config_print_after_sale'] = '出售后打印收据';
//Automatically Email receipt
$lang['config_automatically_email_receipt'] = '自动电子邮件收据';
//Include tax on barcodes
$lang['config_barcode_price_include_tax'] = '在条形码上纳税';
//Disable confirmation for complete sale
$lang['disable_confirmation_sale'] = '禁止确认完成销售';
//Currency Symbol
$lang['config_currency_symbol'] = '货币符号';
//Backup Database
$lang['config_backup_database'] = '备份数据库';
//Restore Database
$lang['config_restore_database'] = '恢复数据库';
//Number Of Items Per Page
$lang['config_number_of_items_per_page'] = '每页数量';
//Date Format
$lang['config_date_format'] = '日期格式';
//Time Format
$lang['config_time_format'] = '时间格式';
//Optimize Database
$lang['config_optimize_database'] = '优化数据库';
//Optimized Database Successfully
$lang['config_database_optimize_successfully'] = '成功优化数据库';
//Payment Types
$lang['config_payment_types'] = '付款类型';
//select .sql file
$lang['select_sql_file'] = '选择.sql文件';
//This allows you to restore your database
$lang['restore_heading'] = '这样可以恢复数据库';
//select .sql file from your computer
$lang['type_file'] = '从您的计算机中选择.sql文件';
//restore
$lang['restore'] = '恢复';
//No sql file is selected
$lang['required_sql_file'] = '没有选择sql文件';
//DataBase is restored successfully
$lang['restore_db_success'] = 'DataBase已成功恢复';
//Are you sure of restoring the database?
$lang['db_first_alert'] = '确定还原数据库吗？';
//Present data will be lost , continue?
$lang['db_second_alert'] = '现在的数据会丢失，继续吗？';
//Password incorrect
$lang['password_error'] = '密码错误';
//Password field cannot be blank
$lang['password_required'] = '密码字段不能为空';
//Restore Database
$lang['restore_database_title'] = '恢复数据库';
//Environment
$lang['config_environment'] = '环境';
//Sandbox
$lang['config_sandbox'] = '砂箱';
//Production
$lang['config_production'] = '生产';
//Default Payment Type
$lang['config_default_payment_type'] = '默认付款类型';
//Only recommend if you have more than 10,000 items or customers
$lang['config_speed_up_note'] = '只有当您拥有超过10,000个物品或客户时才推荐';
//Hide Signature
$lang['config_hide_signature'] = '隐藏签名';
//Round to nearest .05 on receipt
$lang['config_round_cash_on_sales'] = '收到时最接近.05';
//Customers Store Accounts
$lang['config_customers_store_accounts'] = '客户商店帐户';
//Change sale date when suspending sale
$lang['config_change_sale_date_when_suspending'] = '更改销售日期，暂停销售';
//Change sale date when completing suspended sale
$lang['config_change_sale_date_when_completing_suspended_sale'] = '更改销售日期，完成暂停销售';
//Price tiers
$lang['config_price_tiers'] = '价格层';
//Add tier
$lang['config_add_tier'] = '添加层';
//Show receipt after suspending sale
$lang['config_show_receipt_after_suspending_sale'] = '暂停销售后显示收据';
//Backup Overview
$lang['config_backup_overview'] = '备份概述';
//Backing up your data is very important, but can be troublesome with large amount of data. If you have lots of images, items, and sales this can increase the size of your database.
$lang['config_backup_overview_desc'] = '备份数据非常重要，但是对于大量的数据可能会麻烦。如果您有大量图像，项目和销售量，则可以增加数据库的大小。';
//We offer many options for backup to help you decide how to proceed
$lang['config_backup_options'] = '我们提供许多备份选项，以帮助您决定如何继续';
//Clicking "Backup database". This will attempt to download your whole database to a file. If you get a blank screen or can't download the file, try one of the other options.
$lang['config_backup_simple_option'] = '点击“备份数据库”。这将尝试将整个数据库下载到一个文件。如果您获得空白屏幕或无法下载该文件，请尝试其他选项之一。';
//PHPMyAdmin is a popular tool for managing your databases. If you are using the download version with installer, it can be accessed by going to
$lang['config_backup_phpmyadmin_1'] = 'PHPMyAdmin是用于管理数据库的流行工具。如果您正在使用具有安装程序的下载版本，可以通过转到';
//Your username is root and password is what you used during initial installation of PHP POS. Once logged in select your database from the panel on the left. Then select export and then submit the form.
$lang['config_backup_phpmyadmin_2'] = '您的用户名是root，密码是您在初始安装PHP POS时使用的。登录后，从左侧的面板中选择您的数据库。然后选择导出，然后提交表单。';
//If you have installed on your own server that has a control panel such as cpanel, look for the backup module which will often let you download backups of your database.
$lang['config_backup_control_panel'] = '如果您已经安装在具有诸如cpanel这样的控制面板的自己的服务器上，请寻找备份模块，这将常常让您下载数据库的备份。';
//If you have access to the shell and mysqldump on your server, you can try to execute it by clicking the below link. Otherwise  you will need to try other options.
$lang['config_backup_mysqldump'] = '如果您可以访问服务器上的shell和mysqldump，可以单击下面的链接尝试执行它。否则，您将需要尝试其他选项。';
//mysqldump backup has failed. This could be due to a server restriction or the command might not be available. Please try another backup method
$lang['config_mysqldump_failed'] = 'mysqldump备份失败。这可能是由于服务器限制或命令可能不可用。请尝试另一种备份方法';
//Looking for other configuration options? Go to
$lang['config_looking_for_location_settings'] = '寻找其他配置选项？去';
//Module
$lang['config_module'] = '模';
//Calculate Average Cost Price from Receivings
$lang['config_automatically_calculate_average_cost_price_from_receivings'] = '计算收到的平均成本价格';
//Averaging Method
$lang['config_averaging_method'] = '平均方法';
//Historical Average
$lang['config_historical_average'] = '历史平均水平';
//Moving Average
$lang['config_moving_average'] = '移动平均线';
//Hide Dashboard Statistics
$lang['config_hide_dashboard_statistics'] = '隐藏仪表板统计信息';
//Hide Store Account Payments In Reports
$lang['config_hide_store_account_payments_in_reports'] = '在报告中隐藏商店帐户付款';
//Item ID to Show on Sales Interface
$lang['config_id_to_show_on_sale_interface'] = '要在销售界面显示的物品编号';
//Auto Focus On Item Field When using Sales/Receivings Interfaces
$lang['config_auto_focus_on_item_after_sale_and_receiving'] = '使用销售/接收接口时自动对焦项目字段';
//Automatically Show Comments on Receipt
$lang['config_automatically_show_comments_on_receipt'] = '自动显示收据的评论';
//Hide Recent Sales for Customer
$lang['config_hide_customer_recent_sales'] = '隐藏客户最近的销售';
//Spreadsheet Format
$lang['config_spreadsheet_format'] = '电子表格格式';
//CSV
$lang['config_csv'] = 'CSV';
//XLSX
$lang['config_xlsx'] = 'XLSX';
//Disable Giftcard Detection
$lang['config_disable_giftcard_detection'] = '禁用礼品卡检测';
//Disable giftcard subtraction when using giftcard during sale
$lang['config_disable_subtraction_of_giftcard_amount_from_sales'] = '销售时使用礼品卡时禁用礼品卡扣除';
//Always Show Item Grid
$lang['config_always_show_item_grid'] = '始终显示项目网格';
//Legacy Detailed Report Excel Export
$lang['config_legacy_detailed_report_export'] = '传统详细报告Excel导出';
//Print receipt after receiving
$lang['config_print_after_receiving'] = '收到后打印收据';
//Company Information
$lang['config_company_info'] = '公司信息';
//Suspended Sales/Layaways
$lang['config_suspended_sales_layaways_info'] = '暂停销售/折价';
//Application Settings
$lang['config_application_settings_info'] = '应用程序设置';
//Hide barcode on receipts
$lang['config_hide_barcode_on_sales_and_recv_receipt'] = '收据上隐藏条形码';
//Round tier Prices to 2 decimals
$lang['config_round_tier_prices_to_2_decimals'] = '圆形价格为2位小数';
//Group all taxes on receipt
$lang['config_group_all_taxes_on_receipt'] = '收取所有税款';
//Receipt text size
$lang['config_receipt_text_size'] = '收据文字大小';
//Small
$lang['config_small'] = '小';
//Medium
$lang['config_medium'] = '中';
//Large
$lang['config_large'] = '大';
//Extra large
$lang['config_extra_large'] = '超大';
//Select sales person during sale
$lang['config_select_sales_person_during_sale'] = '在销售期间选择销售人员';
//Default sales person
$lang['config_default_sales_person'] = '默认销售人员';
//Require customer for sale
$lang['config_require_customer_for_sale'] = '要求客户出售';
//Hide store account payments from report totals
$lang['config_hide_store_account_payments_from_report_totals'] = '从报告总额中隐藏商店帐户付款';
//Disable sale notifications
$lang['config_disable_sale_notifications'] = '禁用销售通知';
//ID to show on barcode
$lang['config_id_to_show_on_barcode'] = 'ID显示条形码';
//Currency Denominations
$lang['config_currency_denoms'] = '货币面值';
//Currency Value
$lang['config_currency_value'] = '货币价值';
//Add currency denomination
$lang['config_add_currency_denom'] = '添加货币面额';
//Enable Time Clock
$lang['config_enable_timeclock'] = '启用时钟';
//Change Sale Date For New Sale
$lang['config_change_sale_date_for_new_sale'] = '更改新销售的销售日期';
//Don't average, use current received price
$lang['config_dont_average_use_current_recv_price'] = '不平均，使用当前收到的价格';
//Number of recent sales by customer to show
$lang['config_number_of_recent_sales'] = '近期销售量由客户展示';
//Hide suspended Receivings in reports
$lang['config_hide_suspended_recv_in_reports'] = '隐藏报告中的暂停收款';
//Calculate Gift Card Profit When
$lang['config_calculate_profit_for_giftcard_when'] = '计算礼品卡利润时间';
//Selling Gift Card
$lang['config_selling_giftcard'] = '销售礼品卡';
//Redeeming Gift Card
$lang['config_redeeming_giftcard'] = '兑换礼品卡';
//Remove customer contact info from receipt
$lang['config_remove_customer_contact_info_from_receipt'] = '从收据中删除客户联系信息';
//Speed up search queries?
$lang['config_speed_up_search_queries'] = '加快搜索查询？';
//Redirect to sale or receiving screen after printing receipt
$lang['config_redirect_to_sale_or_recv_screen_after_printing_receipt'] = '打印收据后重定向到销售或接收屏幕';
//Enable sounds for status messages
$lang['config_enable_sounds'] = '启用状态消息的声音';
//Charge tax on receivings
$lang['config_charge_tax_on_recv'] = '收费征收税';
//Report Sort Order
$lang['config_report_sort_order'] = '报告排序顺序';
//Oldest first
$lang['config_asc'] = '最旧的';
//Newest first
$lang['config_desc'] = '最新的第一';
//Do NOT group items that are the same
$lang['config_do_not_group_same_items'] = '不要分组相同的项目';
//Show item id on receipt
$lang['config_show_item_id_on_receipt'] = '收到时显示项目ID';
//Show Language Switcher
$lang['config_show_language_switcher'] = '显示语言切换器';
//Do not allow out of stock items to be sold
$lang['config_do_not_allow_out_of_stock_items_to_be_sold'] = '不允许出售物品';
//Number of items per page in grid
$lang['config_number_of_items_in_grid'] = '网格中每页的数量';
//Edit item price if 0 after adding to sale
$lang['config_edit_item_price_if_zero_after_adding'] = '修改商品价格如果0加入销售后';
//Override receipt title
$lang['config_override_receipt_title'] = '覆盖收据标题';
//Automatically print duplicate receipt for credit card transactions
$lang['config_automatically_print_duplicate_receipt_for_cc_transactions'] = '自动打印信用卡交易的重复收据';
//Default type for Grid
$lang['config_default_type_for_grid'] = '网格的默认类型';
//Billing is managed through  <a target="_blank" href="http://paypal.com">Paypal</a>. You can cancel your subscription by clicking <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">here</a>. You can <a href="http://phppointofsale.com/update_billing.php" target="_blank">update billing here</a>.
$lang['config_billing_is_managed_through_paypal'] = '帐单通过<a target="_blank" href="http://paypal.com"> Paypal </a>进行管理。您可以点击<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=BNTRX72M8UZ2E">此处</a>取消订阅。您可以<a href="http://phppointofsale.com/update_billing.php" target="_blank">在此更新结算</a>。';
//Language cannot be saved at application level. However the default admin employee can change the language using the selector in the header of the program
$lang['config_cannot_change_language'] = '语言无法在应用程序级保存。但是，默认管理员员可以使用程序标题中的选择器来更改语言';
//Disable sale quick complete
$lang['disable_quick_complete_sale'] = '禁止销售快速完成';
//Enable fast user switching (password not required)
$lang['config_fast_user_switching'] = '启用快速用户切换（密码不需要）';
//Require employee login before each sale
$lang['config_require_employee_login_before_each_sale'] = '在每次销售之前要求员工登录';
//Reset location when switching employee
$lang['config_reset_location_when_switching_employee'] = '切换员工时重置位置';
//Number of decimals
$lang['config_number_of_decimals'] = '小数位数';
//Let system decide (Recommended)
$lang['config_let_system_decide'] = '让系统决定（推荐）';
//Thousands Separator
$lang['config_thousands_separator'] = '数千分离器';
//Enhanced Search Method
$lang['config_enhanced_search_method'] = '增强搜索方法';
//Hide store account balance on receipt
$lang['config_hide_store_account_balance_on_receipt'] = '收据时隐藏商店帐户余额';
//Decimal Point
$lang['config_decimal_point'] = '小数点';
//Hide out of stock items in grid
$lang['config_hide_out_of_stock_grid'] = '隐藏网格中的库存物品';
//Highlight low inventory items in items module
$lang['config_highlight_low_inventory_items_in_items_module'] = '突出显示项目模块中的低库存项目';
//Sort
$lang['config_sort'] = '分类';
//Enable Customer Loyalty system
$lang['config_enable_customer_loyalty_system'] = '启用客户忠诚度系统';
//Spend amount to point ratio
$lang['config_spend_to_point_ratio'] = '花费点数比';
//Point Value
$lang['config_point_value'] = '点值';
//Hide Points On Receipt
$lang['config_hide_points_on_receipt'] = '隐藏接收点';
//Show Clock in Header
$lang['config_show_clock_on_header'] = '在标题中显示时钟';
//This is visible only on wide screens
$lang['config_show_clock_on_header_help_text'] = '这仅在宽屏幕上可见';
//Enter the amount to spend
$lang['config_loyalty_explained_spend_amount'] = '输入支出金额';
//Enter points to be earned
$lang['config_loyalty_explained_points_to_earn'] = '输入要获得的积分';
//Simple
$lang['config_simple'] = '简单';
//Advanced
$lang['config_advanced'] = '高级';
//Loyalty Program Option
$lang['config_loyalty_option'] = '忠诚计划选项';
//Number of sales for discount
$lang['config_number_of_sales_for_discount'] = '打折销售数量';
//Discount percent earned when reaching sales
$lang['config_discount_percent_earned'] = '达到销售时的折扣百分比';
//Hide sales to discount on receipt
$lang['hide_sales_to_discount_on_receipt'] = '隐藏销售收据打折';
//Hide price on barcodes
$lang['config_hide_price_on_barcodes'] = '隐藏条形码的价格';
//Always Use Global Average Cost Price For A Sale Item's Cost Price. (DO NOT check unless you know what it means)
$lang['config_always_use_average_cost_method'] = '始终使用全球平均成本价格出售物品的成本价格。 （除非你知道这是什么意思）';
//Sales NOT saved
$lang['config_test_mode_help'] = '销售未保存';
//Require customer for suspended sale
$lang['config_require_customer_for_suspended_sale'] = '要求客户暂停销售';
//Default New Items as service items
$lang['config_default_new_items_to_service'] = '默认新项目作为服务项目';
//Prompt for CCV when swiping credit card
$lang['config_prompt_for_ccv_swipe'] = '刷卡时提示CCV';
//Disable store account when over credit limit
$lang['config_disable_store_account_when_over_credit_limit'] = '超过信用限额时禁用商店帐户';
//Mailing Labels Format
$lang['config_mailing_labels_type'] = '邮寄标签格式';
//Session expiration
$lang['config_phppos_session_expiration'] = '会话过期';
//Hours
$lang['config_hours'] = '小时';
//Never
$lang['config_never'] = '决不';
//On Browser Close
$lang['config_on_browser_close'] = '浏览器关闭';
//Do NOT allow items to be sold below cost price
$lang['config_do_not_allow_below_cost'] = '不要让物品以低于成本价出售';
//Store Account Statement Message
$lang['config_store_account_statement_message'] = '存储帐户声明消息';
//Enable Mark Up Calculator
$lang['config_enable_margin_calculator'] = '启用标记计算器';
//Enable quick edit on manage pages
$lang['config_enable_quick_edit'] = '在管理页面上启用快速编辑';
//Show original price on receipt if marked down
$lang['config_show_orig_price_if_marked_down_on_receipt'] = '在收货时显示原价';
//Cancel Account
$lang['config_cancel_account'] = '取消帐户';
//You can update and cancel your billing information by clicking the buttons below:
$lang['config_update_billing'] = '您可以点击下面的按钮来更新和取消您的结算信息：';
//Include child categories when searching or reporting
$lang['config_include_child_categories_when_searching_or_reporting'] = '在搜索或报告时包括子类别';
//Confirm error messages using modal dialogs
$lang['config_confirm_error_messages_modal'] = '使用模态对话框确认错误消息';
//Remove commission from profit in reports
$lang['config_remove_commission_from_profit_in_reports'] = '从报告中的利润中删除佣金';
//Remove points redemption from profit
$lang['config_remove_points_from_profit'] = '清除积分赎回利润';
//Capture signature for all sales
$lang['config_capture_sig_for_all_payments'] = '捕获所有销售的签名';
//Suppliers Store Accounts
$lang['config_suppliers_store_accounts'] = '供应商商店帐户';
//Currency Symbol Location
$lang['config_currency_symbol_location'] = '货币符号位置';
//Before Number
$lang['config_before_number'] = '数字前';
//After Number
$lang['config_after_number'] = '数字后';
//Hide Description on Receipt
$lang['config_hide_desc_on_receipt'] = '隐藏收据说明';
//Default Percent Off
$lang['config_default_percent_off'] = '默认百分比关';
//Default Cost Plus Percent
$lang['config_default_cost_plus_percent'] = '默认成本加百分比';
//Default Tier Percent Type for excel import
$lang['config_default_tier_percent_type_for_excel_import'] = 'Excel导入的默认级别百分比类型';
//Override Tier Name on Receipt
$lang['config_override_tier_name'] = '覆盖收据上的层名称';
//Loyalty points earned not including tax
$lang['config_loyalty_points_without_tax'] = '积分不包括税金';
//Lock prices when unsuspending sale even if they belong to a tier
$lang['config_lock_prices_suspended_sales'] = '即使属于一个层次，也可以锁定销售时的价格';
//Remove Customer Name From Receipt
$lang['config_remove_customer_name_from_receipt'] = '从收货中删除客户名称';
//UPC-12 4 price digits
$lang['config_scale_1'] = 'UPC-12 4价格数字';
//UPC-12 5 Price Digits
$lang['config_scale_2'] = 'UPC-12 5价格数位';
//EAN-13 5 price digits
$lang['config_scale_3'] = 'EAN-13 5个价格数字';
//EAN-13 6 price digits
$lang['config_scale_4'] = 'EAN-13 6价格数字';
//Scale Barcode Format
$lang['config_scale_format'] = '缩放条形码格式';
//Enable Scale
$lang['config_enable_scale'] = '启用缩放';
//Woocommerce Settings
$lang['config_woocommerce_settings_info'] = '电子商务设置';
//Store Location
$lang['config_store_location'] = '商店位置';
//Woocommerce API Secret
$lang['config_woo_api_secret'] = 'Woocommerce API秘密';
//Woocommerce API Url
$lang['config_woo_api_url'] = 'Woocommerce API网址';
//Woocommerce API Key
$lang['config_woo_api_key'] = 'Woocommerce API密钥';
//Ecommerce Platform
$lang['config_ecommerce_settings_info'] = '电子商务平台';
//Select Platform
$lang['config_ecommerce_platform'] = '选择平台';
//Magento Settings
$lang['config_magento_settings_info'] = 'Magento设置';
//Scale Price Divide By
$lang['config_scale_divide_by'] = '规模价格分割';
//Do not force HTTP when needed for EMV Credit Card Processing
$lang['config_do_not_force_http'] = '当EMV信用卡处理需要时，不要强制HTTP';
//Log out automatically when clocking out
$lang['config_logout_on_clock_out'] = '时钟输出时自动注销';
//Override Layaway Name
$lang['config_user_configured_layaway_name'] = '覆盖Layaway名称';
//Virtual Keyboard (On/Off)
$lang['config_virtual_keyboard'] = '虚拟键盘（开/关）';
//Use Tax Values at ALL locations
$lang['config_use_tax_value_at_all_locations'] = '在所有地点使用税值';
//Enable EBT payments
$lang['config_enable_ebt_payments'] = '启用EBT付款';
//Item ID Auto Increment Starting Value
$lang['config_item_id_auto_increment'] = '物品ID自动增量起始值';
//There was an error changing auto_increment for item_id
$lang['config_change_auto_increment_item_id_unsuccessful'] = '更改item_id的auto_increment时出错';
//Item Kit ID Auto Increment Starting Value
$lang['config_item_kit_id_auto_increment'] = '项目编号ID自动增量起始值';
//Sale ID Auto Increment Starting Value
$lang['config_sale_id_auto_increment'] = '销售ID自动增量起始值';
//Receiving ID Auto Increment Starting Value
$lang['config_receiving_id_auto_increment'] = '接收ID自动增量起始值';
//There was an error changing auto_increment for  Iitem_kit_id
$lang['config_change_auto_increment_item_kit_id'] = '更改Iitem_kit_id的auto_increment时发生错误';
//There was an error changing auto_increment for sale_id
$lang['config_change_auto_increment_sale_id'] = '更改sales_id的auto_increment时出错';
//There was an error changing auto_increment for receiving_id
$lang['config_change_auto_increment_receiving_id'] = '更改receive_id的auto_increment时发生错误';
//You can only increase Auto Increment values. Updating them will not affect IDs for items, item kits, sales or receivings that already exist.
$lang['config_auto_increment_note'] = '您只能增加自动增量值。更新它们不会影响已存在的项目，项目套件，销售或收货的ID。';
//Online Price Tier
$lang['config_online_price_tier'] = '在线价格层';
//Email Settings
$lang['config_email_settings_info'] = '电子邮件设置';
//Last Sync Date
$lang['config_last_sync_date'] = '最后同步日期';
//Sync
$lang['config_sync'] = '同步';
//SMTP Encryption
$lang['config_smtp_crypto'] = 'SMTP加密';
//Mail Sending Protocol
$lang['config_email_protocol'] = '邮件发送协议';
//SMTP Server Address
$lang['config_smtp_host'] = 'SMTP服务器地址';
//Email Address
$lang['config_smtp_user'] = '电子邮件地址';
//Email Password
$lang['config_smtp_pass'] = '电子邮件密码';
//SMTP Port
$lang['config_smtp_port'] = 'SMTP端口';
//Character set
$lang['config_email_charset'] = '字符集';
//Newline character
$lang['config_email_newline'] = '换行字符';
//CRLF
$lang['config_email_crlf'] = 'CRLF';
//SMTP Timeout
$lang['config_smtp_timeout'] = 'SMTP超时';
//Send Test Email
$lang['config_send_test_email'] = '发送测试电子邮件';
//Please enter email address to send test email to
$lang['config_please_enter_email_to_send_test_to'] = '请输入发送测试邮件的电子邮件地址';
//Email has been sent successfully
$lang['config_email_succesfully_sent'] = '电子邮件已经发送成功';
//Taxes
$lang['config_taxes_info'] = '税';
//Currency
$lang['config_currency_info'] = '货币';
//Receipt
$lang['config_receipt_info'] = '收据';
//Barcodes
$lang['config_barcodes_info'] = '条形码';
//Customer Loyalty
$lang['config_customer_loyalty_info'] = '客户忠诚度';
//Price Tiers
$lang['config_price_tiers_info'] = '价格层';
//ID Numbers
$lang['config_auto_increment_ids_info'] = '身份证号码';
//Items
$lang['config_items_info'] = '项目';
//Employee
$lang['config_employee_info'] = '雇员';
//Store Accounts
$lang['config_store_accounts_info'] = '商店帐号';
//Sales
$lang['config_sales_info'] = '销售';
//Payment Types
$lang['config_payment_types_info'] = '付款类型';
//Profit Calculation
$lang['config_profit_info'] = '利润计算';
//View Dashboard Statistics
$lang['reports_view_dashboard_stats'] = '查看仪表板统计信息';
//email settings
$lang['config_keyword_email'] = '电子邮件设置';
//company
$lang['config_keyword_company'] = '公司';
//taxes
$lang['config_keyword_taxes'] = '税';
//currency
$lang['config_keyword_currency'] = '货币';
//payment
$lang['config_keyword_payment'] = '付款';
//sales
$lang['config_keyword_sales'] = '销售';
//suspended layaways
$lang['config_keyword_suspended_layaways'] = '暂停摊铺';
//receipt
$lang['config_keyword_receipt'] = '收据';
//profit
$lang['config_keyword_profit'] = '利润';
//barcodes
$lang['config_keyword_barcodes'] = '条形码';
//customer loyalty
$lang['config_keyword_customer_loyalty'] = '客户忠诚度';
//price tiers
$lang['config_keyword_price_tiers'] = '价格层';
//starting auto increment id numbers database
$lang['config_keyword_auto_increment'] = '启动自动递增ID号数据库';
//items
$lang['config_keyword_items'] = '项目';
//employees
$lang['config_keyword_employees'] = '雇员';
//store accounts
$lang['config_keyword_store_accounts'] = '商店帐号';
//application settings
$lang['config_keyword_application_settings'] = '应用设置';
//ecommerce platform
$lang['config_keyword_ecommerce'] = '电子商务平台';
//woocommerce settings ecommerce
$lang['config_keyword_woocommerce'] = '电子商务设置';
//Billing Information
$lang['config_billing_info'] = '账单信息';
//billing cancel update
$lang['config_keyword_billing'] = '帐单取消更新';
//WooCommerce Version
$lang['config_woo_version'] = 'WooCommerce版本';
//Sync item changes
$lang['sync_phppos_item_changes'] = '同步项目更改';
//Sync item changes
$lang['config_sync_phppos_item_changes'] = '同步项目更改';
//Import items into phppos
$lang['config_import_ecommerce_items_into_phppos'] = '将项导入phppos';
//Sync inventory changes
$lang['config_sync_inventory_changes'] = '同步库存更改';
//Export tags to ecommerce
$lang['config_export_phppos_tags_to_ecommerce'] = '将标签导出到电子商务';
//Export categories to ecommerce
$lang['config_export_phppos_categories_to_ecommerce'] = '将类别导出为电子商务';
//Export items to ecommerce
$lang['config_export_phppos_items_to_ecommerce'] = '将项目导出到电子商务';
//Ecommerce Sync Operations
$lang['config_ecommerce_cron_sync_operations'] = '电子商务同步操作';
//Sync Progress
$lang['config_ecommerce_progress'] = '同步进度';
//Are you sure you want to cancel the sync?
$lang['confirmation_woocommerce_cron_cancel'] = '您确定要取消同步吗？';
//Require https for program
$lang['config_force_https'] = '需要https的程序';
//Price Rules
$lang['config_keyword_price_rules'] = '价格规则';
//Disable Price Rules dialog
$lang['config_disable_price_rules_dialog'] = '禁用价格规则对话框';
//Price Rules
$lang['config_price_rules_info'] = '价格规则';
//Prompt to use points when available
$lang['config_prompt_to_use_points'] = '提供时使用点';
//Always print duplicate receipt for all transactions
$lang['config_always_print_duplicate_receipt_all'] = '始终打印所有交易的重复收据';
//Orders And Deliveries
$lang['config_orders_and_deliveries_info'] = '订单和交货';
//Delivery Methods
$lang['config_delivery_methods'] = '交货方式';
//Shipping Providers
$lang['config_shipping_providers'] = '航运公司';
//Expand
$lang['config_expand'] = '扩大';
//Add Delivery Rate
$lang['config_add_delivery_rate'] = '添加交货率';
//Add Shipping Provider
$lang['config_add_shipping_provider'] = '添加货运提供商';
//Delivery Rates
$lang['config_delivery_rates'] = '交货率';
//Delivery Fee
$lang['config_delivery_fee'] = '快递费';
//orders delivery deliveries
$lang['config_keyword_orders_deliveries'] = '订单交货';
//Delivery Fee Tax
$lang['config_delivery_fee_tax'] = '运送费税';
//Add Rate
$lang['config_add_rate'] = '加价';
//Delivery Time In Days
$lang['config_delivery_time'] = '交货时间在天';
//Delivery Rate
$lang['config_delivery_rate'] = '交货率';
//Rate Name
$lang['config_rate_name'] = '价格名称';
//Rate Fee
$lang['config_rate_fee'] = '价格费';
//Rate Tax
$lang['config_rate_tax'] = '价格税';
//Tax Groups
$lang['config_tax_classes'] = '税组';
//Add Tax Group
$lang['config_add_tax_class'] = '添加税组';
//Wide Printer Receipt Format
$lang['config_wide_printer_receipt_format'] = '宽打印机收据格式';
//Default Cost Plus Fixed Amount
$lang['config_default_cost_plus_fixed_amount'] = '默认成本加固定金额';
//Default Tier Fixed Amount for Excel Import
$lang['config_default_tier_fixed_type_for_excel_import'] = 'Excel导入的默认层次固定金额';
//Default Reorder Level When Creating Items
$lang['config_default_reorder_level_when_creating_items'] = '创建项目时的默认重新排序级别';
//Remove customer company name from receipt
$lang['config_remove_customer_company_from_receipt'] = '从收据中删除客户公司名称';
//Import categories into phppos
$lang['config_import_ecommerce_categories_into_phppos'] = '将类别导入phppos';
//Imports tags into phppos
$lang['config_import_ecommerce_tags_into_phppos'] = '将标签导入phppos';
//Shipping Zones
$lang['config_shipping_zones'] = '运输区';
//Add Shipping Zone
$lang['config_add_shipping_zone'] = '添加运送区域';
//No Results
$lang['config_no_results'] = '没有结果';
//Type in a zipcode
$lang['config_zip_search_term'] = '输入邮政编码';
//Searching...
$lang['config_searching'] = '搜索...';
//Tax Group
$lang['config_tax_class'] = '税务组';
//Zone
$lang['config_zone'] = '区';
//Zip Codes
$lang['config_zip_codes'] = '邮政编码';
//Add Zip Code
$lang['config_add_zip_code'] = '添加邮政编码';
//E-Commerce Syncing Logs
$lang['config_ecom_sync_logs'] = '电子商务同步日志';
//Currency Code
$lang['config_currency_code'] = '货币代码';
//Add Currency Exchange Rate
$lang['config_add_currency_exchange_rate'] = '添加货币汇率';
//Exchange Rates
$lang['config_currency_exchange_rates'] = '汇率';
//Exchange Rate
$lang['config_exchange_rate'] = '汇率';
//Item Lookup Order
$lang['config_item_lookup_order'] = '物品查找单';
//Item Id
$lang['config_item_id'] = '物品编号';
//Reset E-Commerce
$lang['config_reset_ecommerce'] = '重置电子商务';
//Are you sure you want to reset e-commerce? This will only reset php point of sale so items are no longer linked
$lang['config_confirm_reset_ecom'] = '你确定要重置电子商务吗？这只会重置php销售点，所以项目不再链接';
//You have reset E-Commerce successfully
$lang['config_reset_ecom_successfully'] = '您已成功重置电子商务';
//Number of Decimals for Quantity On Receipt
$lang['config_number_of_decimals_for_quantity_on_receipt'] = '收货数量的小数位数';
//Enable WIC
$lang['config_enable_wic'] = '启用WIC';
//Store Opening Time
$lang['config_store_opening_time'] = '开店时间';
//Store Closing Time
$lang['config_store_closing_time'] = '商店关闭时间';
//Limit Manual Price Adjustments And Discounts
$lang['config_limit_manual_price_adj'] = '限制手动价格调整和折扣';
$lang['config_always_minimize_menu'] = '始终最小化左侧栏菜单';
$lang['config_do_not_tax_service_items_for_deliveries'] = '不要为交货税服务项目';
$lang['config_paypal_me'] = 'PayPal.me用户名';
?>