<?php
/**
 * Handle cron events.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\Features\Features;
use \Automattic\WooCommerce\Internal\Admin\Notes\AddingAndManangingProducts;
use \Automattic\WooCommerce\Internal\Admin\Notes\ChoosingTheme;
use \Automattic\WooCommerce\Internal\Admin\Notes\CustomizingProductCatalog;
use \Automattic\WooCommerce\Internal\Admin\Notes\FirstDownlaodableProduct;
use \Automattic\WooCommerce\Internal\Admin\Notes\InsightFirstProductAndPayment;
use \Automattic\WooCommerce\Internal\Admin\Notes\MobileApp;
use \Automattic\WooCommerce\Internal\Admin\Notes\NewSalesRecord;
use \Automattic\WooCommerce\Internal\Admin\Notes\TrackingOptIn;
use \Automattic\WooCommerce\Internal\Admin\Notes\OnboardingPayments;
use \Automattic\WooCommerce\Internal\Admin\Notes\PersonalizeStore;
use \Automattic\WooCommerce\Internal\Admin\Notes\EUVATNumber;
use \Automattic\WooCommerce\Internal\Admin\Notes\WooCommercePayments;
use \Automattic\WooCommerce\Internal\Admin\Notes\MarketingJetpack;
use \Automattic\WooCommerce\Internal\Admin\Notes\WooCommerceSubscriptions;
use \Automattic\WooCommerce\Internal\Admin\Notes\MigrateFromShopify;
use \Automattic\WooCommerce\Internal\Admin\Notes\LaunchChecklist;
use \Automattic\WooCommerce\Internal\Admin\Notes\RealTimeOrderAlerts;
use \Automattic\WooCommerce\Admin\RemoteInboxNotifications\DataSourcePoller;
use \Automattic\WooCommerce\Admin\RemoteInboxNotifications\RemoteInboxNotificationsEngine;
use \Automattic\WooCommerce\Internal\Admin\Notes\MerchantEmailNotifications;
use \Automattic\WooCommerce\Internal\Admin\Notes\InsightFirstSale;
use \Automattic\WooCommerce\Internal\Admin\Notes\OnlineClothingStore;
use \Automattic\WooCommerce\Internal\Admin\Notes\FirstProduct;
use \Automattic\WooCommerce\Internal\Admin\Notes\CustomizeStoreWithBlocks;
use \Automattic\WooCommerce\Internal\Admin\Notes\TestCheckout;
use \Automattic\WooCommerce\Internal\Admin\Notes\EditProductsOnTheMove;
use \Automattic\WooCommerce\Internal\Admin\Notes\PerformanceOnMobile;
use \Automattic\WooCommerce\Internal\Admin\Notes\ManageOrdersOnTheGo;
use \Automattic\WooCommerce\Internal\Admin\Notes\AddFirstProduct;
use \Automattic\WooCommerce\Internal\Admin\Schedulers\MailchimpScheduler;
use \Automattic\WooCommerce\Internal\Admin\Notes\CompleteStoreDetails;
use \Automattic\WooCommerce\Internal\Admin\Notes\UpdateStoreDetails;
use \Automattic\WooCommerce\Internal\Admin\Notes\PaymentsRemindMeLater;
use \Automattic\WooCommerce\Internal\Admin\Notes\MagentoMigration;

/**
 * Events Class.
 */
class Events {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Cron event handlers.
	 */
	public function init() {
		add_action( 'wc_admin_daily', array( $this, 'do_wc_admin_daily' ) );
	}

	/**
	 * Daily events to run.
	 *
	 * Note: Order_Milestones::other_milestones is hooked to this as well.
	 */
	public function do_wc_admin_daily() {
		$this->possibly_add_notes();
		$this->possibly_delete_notes();
		$this->possibly_update_notes();

		if ( $this->is_remote_inbox_notifications_enabled() ) {
			DataSourcePoller::get_instance()->read_specs_from_data_sources();
			RemoteInboxNotificationsEngine::run();
		}

		if ( $this->is_merchant_email_notifications_enabled() ) {
			MerchantEmailNotifications::run();
		}

		if ( Features::is_enabled( 'onboarding' ) ) {
			( new MailchimpScheduler() )->run();
		}
	}

	/**
	 * Adds notes that should be added.
	 */
	protected function possibly_add_notes() {
		NewSalesRecord::possibly_add_note();
		MobileApp::possibly_add_note();
		TrackingOptIn::possibly_add_note();
		OnboardingPayments::possibly_add_note();
		PersonalizeStore::possibly_add_note();
		WooCommercePayments::possibly_add_note();
		EUVATNumber::possibly_add_note();
		MarketingJetpack::possibly_add_note();
		WooCommerceSubscriptions::possibly_add_note();
		MigrateFromShopify::possibly_add_note();
		InsightFirstSale::possibly_add_note();
		LaunchChecklist::possibly_add_note();
		OnlineClothingStore::possibly_add_note();
		FirstProduct::possibly_add_note();
		RealTimeOrderAlerts::possibly_add_note();
		CustomizeStoreWithBlocks::possibly_add_note();
		TestCheckout::possibly_add_note();
		EditProductsOnTheMove::possibly_add_note();
		PerformanceOnMobile::possibly_add_note();
		ManageOrdersOnTheGo::possibly_add_note();
		ChoosingTheme::possibly_add_note();
		InsightFirstProductAndPayment::possibly_add_note();
		AddFirstProduct::possibly_add_note();
		AddingAndManangingProducts::possibly_add_note();
		CustomizingProductCatalog::possibly_add_note();
		FirstDownlaodableProduct::possibly_add_note();
		CompleteStoreDetails::possibly_add_note();
		UpdateStoreDetails::possibly_add_note();
		PaymentsRemindMeLater::possibly_add_note();
		MagentoMigration::possibly_add_note();
	}

	/**
	 * Deletes notes that should be deleted.
	 */
	protected function possibly_delete_notes() {
		PaymentsRemindMeLater::delete_if_not_applicable();
	}

	/**
	 * Updates notes that should be updated.
	 */
	protected function possibly_update_notes() {
		NewSalesRecord::possibly_update_note();
		MobileApp::possibly_update_note();
		TrackingOptIn::possibly_update_note();
		OnboardingPayments::possibly_update_note();
		PersonalizeStore::possibly_update_note();
		WooCommercePayments::possibly_update_note();
		EUVATNumber::possibly_update_note();
		MarketingJetpack::possibly_update_note();
		WooCommerceSubscriptions::possibly_update_note();
		MigrateFromShopify::possibly_update_note();
		InsightFirstSale::possibly_update_note();
		LaunchChecklist::possibly_update_note();
		OnlineClothingStore::possibly_update_note();
		FirstProduct::possibly_update_note();
		RealTimeOrderAlerts::possibly_update_note();
		CustomizeStoreWithBlocks::possibly_update_note();
		TestCheckout::possibly_update_note();
		EditProductsOnTheMove::possibly_update_note();
		PerformanceOnMobile::possibly_update_note();
		ManageOrdersOnTheGo::possibly_update_note();
		ChoosingTheme::possibly_update_note();
		InsightFirstProductAndPayment::possibly_update_note();
		AddFirstProduct::possibly_update_note();
		AddingAndManangingProducts::possibly_update_note();
		CustomizingProductCatalog::possibly_update_note();
		FirstDownlaodableProduct::possibly_update_note();
		CompleteStoreDetails::possibly_update_note();
		UpdateStoreDetails::possibly_update_note();
		PaymentsRemindMeLater::possibly_update_note();
		MagentoMigration::possibly_update_note();
	}

	/**
	 * Checks if remote inbox notifications are enabled.
	 *
	 * @return bool Whether remote inbox notifications are enabled.
	 */
	protected function is_remote_inbox_notifications_enabled() {
		// Check if the feature flag is disabled.
		if ( ! Features::is_enabled( 'remote-inbox-notifications' ) ) {
			return false;
		}

		// Check if the site has opted out of marketplace suggestions.
		if ( 'yes' !== get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return false;
		}

		// All checks have passed.
		return true;
	}

	/**
	 * Checks if merchant email notifications are enabled.
	 *
	 * @return bool Whether merchant email notifications are enabled.
	 */
	protected function is_merchant_email_notifications_enabled() {
		// Check if the feature flag is disabled.
		if ( 'yes' !== get_option( 'woocommerce_merchant_email_notifications', 'no' ) ) {
			return false;
		}

		// All checks have passed.
		return true;
	}
}
