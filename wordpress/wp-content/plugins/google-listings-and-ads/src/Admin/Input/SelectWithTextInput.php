<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Admin\Input;

defined( 'ABSPATH' ) || exit;

/**
 * Class Select
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Admin\Input
 */
class SelectWithTextInput extends Input {

	public const CUSTOM_INPUT_KEY = '_gla_custom_value';
	public const SELECT_INPUT_KEY = '_gla_select';

	/**
	 * SelectWithTextInput constructor.
	 */
	public function __construct() {
		$select_input = ( new Select() )->set_id( self::SELECT_INPUT_KEY )
										->set_name( self::SELECT_INPUT_KEY );
		$this->add( $select_input );

		$custom_input = ( new Text() )->set_id( self::CUSTOM_INPUT_KEY )
			->set_label( __( 'Enter your value', 'google-listings-and-ads' ) )
			->set_name( self::CUSTOM_INPUT_KEY );
		$this->add( $custom_input );

		parent::__construct( 'select-with-text-input', 'google-listings-and-ads/product-select-with-text-field' );
	}

	/**
	 * @return array
	 */
	public function get_options(): array {
		return $this->get_select_input()->get_options();
	}

	/**
	 * @param array $options
	 *
	 * @return $this
	 */
	public function set_options( array $options ): SelectWithTextInput {
		$this->get_select_input()->set_options( $options );

		return $this;
	}

	/**
	 * @param string|null $label
	 *
	 * @return InputInterface
	 */
	public function set_label( ?string $label ): InputInterface {
		$this->get_select_input()->set_label( $label );

		return parent::set_label( $label );
	}

	/**
	 * @param string|null $description
	 *
	 * @return InputInterface
	 */
	public function set_description( ?string $description ): InputInterface {
		$this->get_select_input()->set_description( $description );

		return parent::set_description( $description );
	}

	/**
	 * @return Select
	 */
	protected function get_select_input(): Select {
		return $this->children[ self::SELECT_INPUT_KEY ];
	}

	/**
	 * @return Text
	 */
	protected function get_custom_input(): Text {
		return $this->children[ self::CUSTOM_INPUT_KEY ];
	}

	/**
	 * Return the data used for the input's view.
	 *
	 * @return array
	 */
	public function get_view_data(): array {
		$view_data = parent::get_view_data();

		$select_input = $view_data['children'][ self::SELECT_INPUT_KEY ];
		$custom_input = $view_data['children'][ self::CUSTOM_INPUT_KEY ];

		// add custom classes
		$view_data['gla_wrapper_class']  = $view_data['gla_wrapper_class'] ?? '';
		$view_data['gla_wrapper_class'] .= ' select-with-text-input';

		$custom_input['wrapper_class'] = 'custom-input';

		// add custom value option
		$select_input['options'][ self::CUSTOM_INPUT_KEY ] = __( 'Enter a custom value', 'google-listings-and-ads' );

		if ( $this->is_readonly ) {
			$select_input['custom_attributes'] = [
				'disabled' => 'disabled',
			];
			$custom_input['custom_attributes'] = [
				'readonly' => 'readonly',
			];
		}

		$view_data['children'][ self::CUSTOM_INPUT_KEY ] = $custom_input;
		$view_data['children'][ self::SELECT_INPUT_KEY ] = $select_input;

		return $view_data;
	}

	/**
	 * Set the form's data.
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function set_data( $data ): void {
		if ( empty( $data ) ) {
			$this->get_select_input()->set_data( null );
			$this->get_custom_input()->set_data( null );
			return;
		}

		$select_value = is_array( $data ) ? $data[ self::SELECT_INPUT_KEY ] ?? '' : $data;
		$custom_value = is_array( $data ) ? $data[ self::CUSTOM_INPUT_KEY ] ?? '' : $data;
		if ( ! isset( $this->get_options()[ $select_value ] ) ) {
			$this->get_select_input()->set_data( self::CUSTOM_INPUT_KEY );
			$this->get_custom_input()->set_data( $custom_value );
			$this->data = $custom_value;
		} else {
			$this->get_select_input()->set_data( $select_value );
			$this->data = $select_value;
		}
	}

	/**
	 * Return the attributes of block config used for the input's view within the Product Block Editor.
	 *
	 * @return array
	 */
	public function get_block_attributes(): array {
		$options = [];

		foreach ( $this->get_options() as $key => $value ) {
			$options[] = [
				'label' => $value,
				'value' => $key,
			];
		}

		$options[] = [
			'label' => __( 'Enter a custom value', 'google-listings-and-ads' ),
			'value' => self::CUSTOM_INPUT_KEY,
		];

		$this->set_block_attribute( 'options', $options );
		$this->set_block_attribute( 'customInputValue', self::CUSTOM_INPUT_KEY );

		return parent::get_block_attributes();
	}
}
