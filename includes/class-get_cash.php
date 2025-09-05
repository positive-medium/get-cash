<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'Get_Cash' ) ) {
    class Get_Cash {
        private $get_cash_options;

        public function __construct() {
            if ( is_admin() ) {
                add_action( 'admin_init', array($this, 'get_cash_page_init') );
            }
        }

        public function get_cash_option( $option ) {
            if ( empty( $option ) ) {
                return '';
            }
            // if ( !empty( $this->get_cash_options ) ) {
            // 	$get_cash_options = $this->get_cash_options;
            // } else {
            // 	$get_cash_options = get_option( 'get_cash_option_name' ); // Array of All Options
            // 	$this->get_cash_options = $get_cash_options;
            // }
            $get_cash_options = get_option( 'get_cash_option_name' );
            // Array of All Options
            return ( is_array( $get_cash_options ) && array_key_exists( $option, $get_cash_options ) ? wp_kses_post( $get_cash_options[$option] ) : '' );
        }

        public function get_cash_page_init() {
            register_setting( 
                'get_cash_option_group',
                // option_group
                'get_cash_option_name',
                // option_name
                array($this, 'get_cash_sanitize')
             );
            $new = " <sup style='color:#0c0;'>NEW</sup>";
            $improved = " <sup style='color:#0c0;'>IMPROVED</sup>";
            $comingSoon = " <sup style='color:#00c;'>COMING SOON</sup>";
            global $getcash_fs;
            $upgrade_url = getcash_fs()->get_upgrade_url();
            $pro = '<a style="text-decoration:none" href="' . $upgrade_url . '" target="_blank"><sup style="color:red">available in PRO</sup></a>' . '<p>Cash App/Venmo/PayPal logos and specifying amount in shortcode <a style="text-decoration:none" href="https://theafricanboss.com/get-cash/" target="_blank"><sup style="color:red">also available in PRO</sup></a></p>';
            $edit_with_pro = ' <a style="text-decoration:none" href="' . $upgrade_url . '" target="_blank"><sup style="color:red">APPLY CHANGES WITH PRO</sup></a>';
            /*
             * Section Payments info
             */
            add_settings_section(
                'get_cash_required_info_section',
                // id
                'Add Receiver info',
                // title
                array($this, 'get_cash_section_info'),
                // callback
                'get-cash-admin'
            );
            add_settings_field(
                'receiver_cash_app',
                // id
                'Add your Cash App $cashtag (example: our cashtag is <a href="https://cash.app/theafricanboss/1" target="_blank">$theafricanboss</a>)',
                // title
                array($this, 'get_cash_receiver_cash_app_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_required_info_section'
            );
            add_settings_field(
                'receiver_venmo',
                // id
                'Add your venmo username (example: our username is <a href="https://venmo.com/theafricanboss?txn=pay&amount=1&note=Thank you for the plugin" target="_blank">theafricanboss</a>)',
                // title
                array($this, 'get_cash_receiver_venmo_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_required_info_section'
            );
            add_settings_field(
                'receiver_paypal',
                // id
                'Add your PayPal.me username (example: our username is <a href="https://paypal.me/theafricanboss/1" target="_blank">theafricanboss</a>)',
                // title
                array($this, 'get_cash_receiver_paypal_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_required_info_section'
            );
            /*
             * Section Zelle info
             */
            add_settings_section(
                'get_cash_additional_info_section',
                // id
                'Input Zelle or Additional info',
                // title
                array($this, 'get_cash_section_additional_info'),
                // callback
                'get-cash-admin'
            );
            add_settings_field(
                'receiver_no',
                // id
                'Receiver Phone Number',
                // title
                array($this, 'get_cash_receiver_no_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_additional_info_section'
            );
            add_settings_field(
                'receiver_email',
                // id
                'Receiver Email',
                // title
                array($this, 'get_cash_receiver_email_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_additional_info_section'
            );
            add_settings_field(
                'receiver_owner',
                // id
                'Receiver Name',
                // title
                array($this, 'get_cash_receiver_owner_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_additional_info_section'
            );
            /*
             * Section PRO
             */
            add_settings_section(
                'get_cash_premium_features_section',
                // id
                'Premium Features' . $pro,
                // title
                array($this, 'get_cash_section_premium_features'),
                // callback
                'get-cash-admin'
            );
            add_settings_field(
                'donate_button_text',
                // id
                'Change Donate Button Text' . $edit_with_pro,
                // title
                array($this, 'get_cash_donate_button_text_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_premium_features_section'
            );
            add_settings_field(
                'donate_button_display',
                // id
                'Full width Centered On/Off' . $edit_with_pro,
                // title
                array($this, 'get_cash_donate_button_display_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_premium_features_section'
            );
            add_settings_field(
                'donate_button_shadow',
                // id
                'Shadow On/Off' . $edit_with_pro,
                // title
                array($this, 'get_cash_donate_button_shadow_callback'),
                // callback
                'get-cash-admin',
                // page
                'get_cash_premium_features_section'
            );
        }

        /*
         * Fields sanitize function
         */
        public function get_cash_sanitize( $input ) {
            $sanitary_values = array();
            if ( isset( $input['receiver_cash_app'] ) ) {
                // $cash_app = strpos($input['receiver_cash_app'], '$') !== false ? $input['receiver_cash_app'] : '$' . $input['receiver_cash_app'];
                // $sanitary_values['receiver_cash_app'] = trim(wp_kses_post( sanitize_text_field( str_replace(' ', '', $cash_app) ) ));
                $sanitary_values['receiver_cash_app'] = trim( wp_kses_post( sanitize_text_field( $input['receiver_cash_app'] ) ) );
            }
            if ( isset( $input['receiver_venmo'] ) ) {
                $sanitary_values['receiver_venmo'] = trim( wp_kses_post( sanitize_text_field( str_replace( ' ', '', str_replace( '@', '', $input['receiver_venmo'] ) ) ) ) );
            }
            if ( isset( $input['receiver_paypal'] ) ) {
                $sanitary_values['receiver_paypal'] = trim( wp_kses_post( sanitize_text_field( str_replace( ' ', '', $input['receiver_paypal'] ) ) ) );
            }
            if ( isset( $input['receiver_no'] ) ) {
                $sanitary_values['receiver_no'] = trim( wp_kses_post( sanitize_text_field( $input['receiver_no'] ) ) );
            }
            if ( isset( $input['receiver_owner'] ) ) {
                $sanitary_values['receiver_owner'] = trim( wp_kses_post( sanitize_text_field( $input['receiver_owner'] ) ) );
            }
            if ( isset( $input['receiver_email'] ) ) {
                $sanitary_values['receiver_email'] = trim( wp_kses_post( sanitize_text_field( $input['receiver_email'] ) ) );
            }
            if ( isset( $input['donate_button_text'] ) ) {
                $sanitary_values['donate_button_text'] = trim( wp_kses_post( sanitize_text_field( $input['donate_button_text'] ) ) );
            }
            if ( isset( $input['donate_button_shadow'] ) ) {
                $sanitary_values['donate_button_shadow'] = wp_kses_post( $input['donate_button_shadow'] );
            }
            if ( isset( $input['donate_button_display'] ) ) {
                $sanitary_values['donate_button_display'] = wp_kses_post( $input['donate_button_display'] );
            }
            return $sanitary_values;
        }

        public function get_cash_sanitize_value( $value ) {
            return trim( wp_kses_post( sanitize_text_field( $value ) ) );
        }

        /*
         * Sections callback functions
         */
        public function get_cash_section_info() {
            echo __( '', GET_CASH_PLUGIN_TEXT_DOMAIN );
        }

        public function get_cash_section_additional_info() {
            echo __( '', GET_CASH_PLUGIN_TEXT_DOMAIN );
        }

        public function get_cash_section_premium_features() {
            echo __( '', GET_CASH_PLUGIN_TEXT_DOMAIN );
        }

        /*
         * Fields callback functions
         */
        public function get_cash_receiver_cash_app_callback() {
            if ( !empty( $this->get_cash_option( 'receiver_cash_app' ) ) ) {
                $test = '<a class="link-primary" href="https://cash.me/' . esc_attr( $this->get_cash_option( 'receiver_cash_app' ) ) . '" target="_blank">Test</a>';
            } else {
                $test = null;
            }
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_cash_app]" id="receiver_cash_app" value="%s"> ' . $test, esc_attr( $this->get_cash_option( 'receiver_cash_app' ) ) );
        }

        public function get_cash_receiver_venmo_callback() {
            if ( !empty( $this->get_cash_option( 'receiver_venmo' ) ) ) {
                $test = '<a class="link-primary" href="https://venmo.com/' . esc_attr( wp_kses_post( $this->get_cash_option( 'receiver_venmo' ) ) ) . '?txn=pay&amount=0.01&note=Thank you" target="_blank">Test</a>';
            } else {
                $test = null;
            }
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_venmo]" id="receiver_venmo" value="%s">' . $test, esc_attr( $this->get_cash_option( 'receiver_venmo' ) ) );
        }

        public function get_cash_receiver_paypal_callback() {
            if ( !empty( $this->get_cash_option( 'receiver_paypal' ) ) ) {
                $test = '<a class="link-primary" href="https://paypal.me/' . esc_attr( $this->get_cash_option( 'receiver_paypal' ) ) . '" target="_blank">Test</a>';
            } else {
                $test = null;
            }
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_paypal]" id="receiver_paypal" value="%s">' . $test, esc_attr( $this->get_cash_option( 'receiver_paypal' ) ) );
        }

        public function get_cash_receiver_no_callback() {
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_no]" id="receiver_no" value="%s">', esc_attr( $this->get_cash_option( 'receiver_no' ) ) );
        }

        public function get_cash_receiver_owner_callback() {
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_owner]" id="receiver_owner" value="%s">', esc_attr( $this->get_cash_option( 'receiver_owner' ) ) );
        }

        public function get_cash_receiver_email_callback() {
            printf( '<input class="gc-text" type="text" name="get_cash_option_name[receiver_email]" id="receiver_email" value="%s">', esc_attr( $this->get_cash_option( 'receiver_email' ) ) );
        }

        // PRO Features
        public function get_cash_donate_button_text_callback() {
            printf( '<input disabled class="gc-text" type="text" name="get_cash_option_name[donate_button_text]" id="donate_button_text" value="%s">', esc_attr( $this->get_cash_option( 'donate_button_text' ) ) );
        }

        public function get_cash_donate_button_display_callback() {
            printf( '<input disabled class="gc-checkbox" type="checkbox" name="get_cash_option_name[donate_button_display]" id="donate_button_display" value="donate_button_display" %s>' . '<label for="donate_button_display"> Enable / Disable</label>', ( $this->get_cash_option( 'donate_button_display' ) === 'donate_button_display' ? 'checked' : '' ) );
        }

        public function get_cash_donate_button_shadow_callback() {
            printf( '<input disabled checked class="gc-checkbox" type="checkbox" name="get_cash_option_name[donate_button_shadow]" id="donate_button_shadow" value="donate_button_shadow" %s>' . '<label for="donate_button_shadow"> Enable / Disable</label>', ( $this->get_cash_option( 'donate_button_shadow' ) === 'donate_button_shadow' ? 'checked' : '' ) );
        }

        // public function get_cash_cashapp_payment_url($amount, $note = '') {
        // 	$receiver_cash_app = $this->get_cash_option('receiver_cash_app');
        // 	if ( empty($receiver_cash_app) ) return '';
        // 	$payment_url = 'https://cash.app/'. $receiver_cash_app;
        // 	if (floatval($amount) > 0) $payment_url .= "/$amount";
        // 	// if ($note) $payment_url .= '?note=' . $note;
        // 	return esc_attr($payment_url);
        // }
        // public function get_cash_cashapp_qrcode_url($amount, $note = '') {
        // 	$payment_url = $this->get_cash_cashapp_payment_url($amount, $note);
        // 	if (empty($payment_url)) return '';
        // 	$qr_code_url = "https://emailreceipts.io/qr?d=150&t=" . urlencode( $payment_url );
        // 	return esc_attr($qr_code_url);
        // }
        // public function get_cash_cashapp_qrcode_html($amount, $note = '') {
        // 	$payment_url = $this->get_cash_cashapp_payment_url($amount, $note);
        // 	$qr_code_url = $this->get_cash_cashapp_qrcode_url($amount, $note);
        // 	if (empty($qr_code_url) || empty($payment_url)) return '';
        // 	if ( getcash_fs()->is_plan__premium_only('pro') ) {
        // 		$default_qrcode_html = '<a href="' . $payment_url . '" target="_blank"><div id="get_cash_cashapp_qrcode"><img class="logo-qr mb-1" width="150px" height="150px" alt="' . $this->method_title . ' QR Code" src="' . $qr_code_url . '" /></div></a>';
        // 		$payment_button_html = '<a class="btn btn-dark" role="button" href="' . $payment_url . '" target="_blank" style="display: flex;max-width: fit-content;text-decoration: none;color: #fff;background-color: #212529;border-color: #212529;padding: 10px 35px;border-radius: 30px;">
        // 		Open Cash App  <img width="30px" height="30px" alt="Cash App logo" src="' . esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/cashapp_35.png' ) . '" />
        // 		</a>';
        // 		if ( 'yes' === $this->display_cashapp_logo_button ) {
        // 			// 'yes' => 'Display BOTH the logo image and QR code button on the checkout page',
        // 			$qrcode_html = $default_qrcode_html . '<p class="text-center mb-1">' . esc_html__( 'Scan with your Camera app', GET_CASH_PLUGIN_TEXT_DOMAIN ) . '<br />' . esc_html__( 'or click the button below', GET_CASH_PLUGIN_TEXT_DOMAIN) . '</p>' . $payment_button_html;
        // 		} else {
        // 			// 'no' => 'Display ONLY the logo image button on the checkout page',
        // 			$qrcode_html = '<p class="text-center mb-1">' . esc_html__( 'Click the button below', GET_CASH_PLUGIN_TEXT_DOMAIN) . '</p>' . $payment_button_html;
        // 		}
        // 	} else {
        // 		$qrcode_html = '<p class="get-cash-cashapp">' . esc_html__('Click', GET_CASH_PLUGIN_TEXT_DOMAIN) . ' >
        // 		<a href="' . $payment_url . '" target="_blank"><img width="150" height="150" class="logo-qr" alt="Cash App Link" src="' . esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/cashapp.png' ) . '"></a> ' .
        // 		esc_html__( 'or Scan', GET_CASH_PLUGIN_TEXT_DOMAIN ) . ' > <a href="' . $payment_url . '" target="_blank"><img width="150" height="150" class="logo-qr" alt="Cash App Link" src="' . $qr_code_url . '"></a></p>';
        // 	}
        // 	return wp_kses_post($qrcode_html);
        // }
        // public function get_cash_venmo_payment_url($amount, $note = '') {
        // 	$receiver_venmo = $this->get_cash_option('receiver_venmo');
        // 	if (!$receiver_venmo) return '';
        // 	$payment_url = "https://venmo.com/{$receiver_venmo}?txn=pay";
        // 	$domain = !empty(parse_url(get_bloginfo('url'))) ? parse_url(get_bloginfo('url'))['host'] : null;
        // 	if ( getcash_fs()->is_plan__premium_only('pro') ) {
        // 		$venmo_note = !empty($this->venmo_note) ? esc_html__( $this->venmo_note ) : sprintf( esc_html__( 'Order from %s', GET_CASH_PLUGIN_TEXT_DOMAIN ), $domain );
        // 		if ( !empty($amount) && $amount != '0' ) {
        // 			$payment_url .= "&amount=$amount&note={$venmo_note}";
        // 		} else {
        // 			$payment_url .= "&note=Thank you";
        // 		}
        // 	} else {
        // 		if ( !empty($amount) && $amount != '0' ) {
        // 			$venmo_note = sprintf( esc_html__( 'Order from %s', GET_CASH_PLUGIN_TEXT_DOMAIN ), $domain );
        // 			$payment_url .= "&amount=$amount&note={$venmo_note}";
        // 		} else {
        // 			$payment_url .= "&note=Thank you";
        // 		}
        // 	}
        // 	return esc_attr($payment_url);
        // }
        // public function get_cash_venmo_qrcode_url($amount, $note = '') {
        // 	$payment_url = $this->get_cash_venmo_payment_url($amount, $note);
        // 	if (empty($payment_url)) return '';
        // 	$qr_code_url = "https://emailreceipts.io/qr?d=150&t=" . urlencode( $payment_url );
        // 	return esc_attr($qr_code_url);
        // }
        // public function get_cash_venmo_qrcode_html($amount, $note = '') {
        // 	$payment_url = $this->get_cash_venmo_payment_url($amount, $note);
        // 	$qr_code_url = $this->get_cash_venmo_qrcode_url($amount, $note);
        // 	if (empty($qr_code_url) || empty($payment_url)) return '';
        // 	if ( getcash_fs()->is_plan__premium_only('pro') ) {
        // 		$default_qrcode_html = '<a href="' . $payment_url . '" target="_blank"><div id="get_cash_venmo_qrcode"><img class="logo-qr mb-1" width="150px" height="150px" alt="' . $this->method_title . ' QR Code" src="' . $qr_code_url . '" /></div></a>';
        // 		$payment_button_html = '<a class="btn btn-dark" role="button" href="' . $payment_url . '" target="_blank" style="display: flex;max-width: fit-content;text-decoration: none;color: #fff;background-color: #3396cd;border-color: #3396cd;padding: 10px 35px;border-radius: 30px;">
        // 		Open Venmo <img width="30px" height="30px" alt="' . $this->method_title . ' logo" src="' . esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/venmo_35.png' ) . '" />
        // 		</a>';
        // 		if ( 'yes' === $this->display_venmo_logo_button ) {
        // 			$qrcode_html = $default_qrcode_html . '<p class="text-center mb-1">' . esc_html__( 'Scan with your Camera app', GET_CASH_PLUGIN_TEXT_DOMAIN ) . '<br />' . esc_html__( 'or click the button below', GET_CASH_PLUGIN_TEXT_DOMAIN) . '</p>' . $payment_button_html;
        // 		} else {
        // 			$payment_button_html = '<a class="btn btn-dark" role="button" href="' . $payment_url . '" target="_blank" style="width: 100%;max-width: fit-content;text-decoration: none;color: #fff;background-color: #3396cd;border-color: #3396cd;padding: 10px 35px;border-radius: 30px;">
        // 			Open Venmo <img width="30px" height="30px" alt="' . $this->method_title . ' logo" src="' . esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/venmo_35.png' ) . '" />
        // 			</a>';
        // 			$qrcode_html = '<p class="mb-1">' . esc_html__( 'Click the button below', GET_CASH_PLUGIN_TEXT_DOMAIN) . '</p>' . $payment_button_html;
        // 		}
        // 	} else {
        // 		$qrcode_html = '<p class="get-cash-venmo">' . esc_html__('Click', GET_CASH_PLUGIN_TEXT_DOMAIN) . ' >
        // 		<a href="' . $payment_url . '" target="_blank"><img width="150" height="150" class="logo-qr" alt="' . $this->method_title . ' Link" src="' . esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/venmo.png' ) . '"></a> ' .
        // 		esc_html__( 'or Scan', GET_CASH_PLUGIN_TEXT_DOMAIN ) . ' > <a href="' . $payment_url . '" target="_blank"><img width="150" height="150" class="logo-qr" alt="' . $this->method_title . ' Link" src="' . $qr_code_url . '"></a></p>';
        // 	}
        // 	return wp_kses_post($qrcode_html);
        // }
        // // $receiver_paypal = $this->get_cash_option('receiver_paypal');
        // public function get_cash_zelle_url($amount = 0) {
        // 	$receiver_zelle_qrcode = $this->get_cash_option('zelle_qrcode');
        // 	$receiver_zelle_number = $this->get_cash_option('receiver_no');
        // 	$receiver_zelle_name = $this->get_cash_option('receiver_owner');
        // 	$receiver_zelle_email = $this->get_cash_option('receiver_email');
        // 	$payment_url = "";
        // 	if ( !empty($receiver_zelle_qrcode) && strpos($receiver_zelle_qrcode, wp_upload_dir()['baseurl']) !== false ) {
        // 		try {
        // 			$api_response = wp_remote_get( 'https://api.qrserver.com/v1/read-qr-code/?fileurl=' . urlencode(trim($receiver_zelle_qrcode)) );
        // 			$response = ! is_wp_error( $api_response ) ? wp_remote_retrieve_body( $api_response ) : null;
        // 			$result = $response ? json_decode( $response, true ) : null;
        // 			if ( !empty($result) && json_last_error() === JSON_ERROR_NONE && !empty($result[0]['type']) && $result[0]['type'] == 'qrcode' ) {
        // 				$data = $result[0]['symbol'][0]['data'];
        // 				if ( !empty($data) && strpos($data, 'https://enroll.zellepay.com/qr-codes?data=') !== false ) {
        // 					$payment_url = esc_attr(trim($data));
        // 				}
        // 			}
        // 		} catch (\Throwable $th) {
        // 			// echo ( "zelle_url: " . $th->getMessage(), 'error' );
        // 		} catch (\Exception $e) {
        // 			// echo ( "zelle_url: " . $e->getMessage(), 'error' );
        // 		}
        // 		return !empty($payment_url) ? esc_attr($payment_url) : esc_attr(trim($receiver_zelle_qrcode));
        // 	} else if ( !empty($receiver_zelle_qrcode) ) {
        // 		return esc_attr(trim($receiver_zelle_qrcode));
        // 	}
        // 	return esc_attr($payment_url);
        // }
        // public function get_cash_zelle_qrcode_url($amount = 0) {
        // 	$receiver_zelle_qrcode = $this->get_cash_option('zelle_qrcode');
        // 	if ( !empty($receiver_zelle_qrcode) && strpos($receiver_zelle_qrcode, 'https://enroll.zellepay.com/qr-codes?data=') === false ) return esc_attr(trim($receiver_zelle_qrcode));
        // 	$qr_code_url = "";
        // 	$payment_url = $this->get_cash_zelle_url($amount);
        // 	if (!empty(trim($payment_url))) {
        // 		$qr_code_url = esc_attr( "https://emailreceipts.io/qr?d=150&t=" . urlencode( wp_kses_post( $payment_url ) ) );
        // 	}
        // 	return esc_attr($qr_code_url);
        // }
        // public function get_cash_zelle_qrcode($amount = 0, $type = "simple") {
        // 	$receiver_zelle_qrcode = $this->get_cash_option('zelle_qrcode');
        // 	$qr_code = "";
        // 	$payment_url = $this->get_cash_zelle_url($amount);
        // 	$qr_code_url = $this->get_cash_zelle_qrcode_url($amount);
        // 	if ( !empty($receiver_zelle_qrcode) && strpos($receiver_zelle_qrcode, 'https://enroll.zellepay.com/qr-codes?data=') === false ) {
        // 		$qr_code_url = esc_attr(trim($receiver_zelle_qrcode));
        // 		$qr_code = '<img style="float: none!important; min-height:300px; min-width:300px; max-height:auto!important; max-width:300px!important;" alt="payment wallet link" src="' . esc_attr( $qr_code_url ) . '">';
        // 		$qr_code = '<a class="qr" href="'. esc_url( $payment_url ) . '" target="_blank">' . $qr_code . '</a>';
        // 		return wp_kses_post($qr_code);
        // 	}
        // 	if ( empty(trim($qr_code_url)) ) return $qr_code;
        // 	if ($type = "advanced") {
        // 		$qr_code .= '<a href="'. esc_url( $payment_url ) . '" target="_blank">';
        // 		// $qr_code .= '<p>' . esc_html__( 'If using the Zelle app, scan/click below', GET_CASH_PLUGIN_TEXT_DOMAIN ) . ':</p>';
        // 		$default_qrcode = '<img class="logo-qr mb-1" width="150px" height="150px" src="'. esc_attr( $qr_code_url ) . '" />';
        // 		$qr_code .= '<div id="">' . $default_qrcode . '</div>';
        // 		$qr_code .= '</a><p class="text-center mb-1">' . esc_html__( 'Scan with your Camera app', GET_CASH_PLUGIN_TEXT_DOMAIN ) . '<br />' . esc_html__( 'or click the button below', GET_CASH_PLUGIN_TEXT_DOMAIN) . '</p>
        // 		<a class="btn btn-dark" role="button" href="'. esc_url( $payment_url ) . '" target="_blank" style="padding: 10px 35px;border-radius: 30px;">Pay with Zelle  <img width="30px" height="30px" alt="Zelle logo" src="'. esc_attr( GET_CASH_PLUGIN_DIR_URL . 'assets/images/zelle_35.png' ) . '" /></a>';
        // 	} else {
        // 		$qr_code = '<a class="logo-qr" href="'. esc_url( $payment_url ) . '" target="_blank"><img style="float: none!important; max-height:150px!important; max-width:100px!important;" alt="payment wallet link" src="' . esc_attr( $qr_code_url ) . '"></a>';
        // 	}
        // 	return wp_kses_post($qr_code);
        // }
    }

}
$get_cash = new Get_Cash();
/*
* Retrieve values with:
* $get_cash_options = get_option( 'get_cash_option_name' ); // Array of All Options
* $receiver_cash_app = $this->get_cash_option('receiver_cash_app'); // Receiver Cash App
*/