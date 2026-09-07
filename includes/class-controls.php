<?php
/**
 * Unfold for Elementor - Controls Manager
 *
 * Injects Unfold controls into the Advanced tab for all Elementor elements
 * (Widgets, Containers, Sections, Columns).
 *
 * @package Dynime\UnfoldElementor
 */

namespace Dynime\UnfoldElementor\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class Controls {

	/**
	 * Singleton instance.
	 *
	 * @var Controls|null
	 */
	protected static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Controls
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into all Elementor element types
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/element/container/section_advanced/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10, 2 );
	}

	/**
	 * Register Unfold controls.
	 *
	 * @param Element_Base $element
	 * @param string       $section_id
	 */
	public function register_controls( $element, $section_id ) {
		if ( $element->get_controls( 'unfold_elementor_enable' ) ) {
			return;
		}

		$this->register_main_section( $element );
		$this->register_overlay_section( $element );
		$this->register_style_section( $element );
	}

	/**
	 * Register Main Unfold Section in Advanced Tab.
	 *
	 * @param Element_Base $element
	 */
	protected function register_main_section( $element ) {
		$element->start_controls_section(
			'section_unfold_elementor',
			array(
				'label' => esc_html__( 'Unfold', 'unfold-for-elementor' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'unfold_elementor_enable',
			array(
				'label'              => esc_html__( 'Enable Unfold', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'description'        => esc_html__( 'Make this entire element/container expandable and collapsible.', 'unfold-for-elementor' ),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_mode',
			array(
				'label'              => esc_html__( 'Fold Mode', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'height'          => esc_html__( 'Fixed / Collapsed Height', 'unfold-for-elementor' ),
					'content_preview' => esc_html__( 'Content Preview Percentage', 'unfold-for-elementor' ),
				),
				'default'            => 'height',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_collapsed_height',
			array(
				'label'              => esc_html__( 'Collapsed Height', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'vh', 'rem', '%' ),
				'range'              => array(
					'px'  => array( 'min' => 0, 'max' => 2500, 'step' => 1 ),
					'vh'  => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					'rem' => array( 'min' => 0, 'max' => 100, 'step' => 0.5 ),
					'%'   => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 160,
				),
				'tablet_default'     => array(
					'unit' => 'px',
					'size' => 140,
				),
				'mobile_default'     => array(
					'unit' => 'px',
					'size' => 120,
				),
				'description'        => esc_html__( 'Set to 0px to completely hide the folded content initially until expanded.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
					'unfold_elementor_mode'   => 'height',
				),
				'selectors'          => array(
					'{{WRAPPER}}.unfold-elementor--collapsed > .unfold-elementor__inner, {{WRAPPER}}.unfold-elementor--collapsed > .e-con-inner, {{WRAPPER}}.unfold-elementor--collapsed > .elementor-container, {{WRAPPER}}.unfold-elementor--collapsed > .elementor-widget-container, {{WRAPPER}}.unfold-elementor--collapsed > .elementor-widget-wrap' => 'max-height: {{SIZE}}{{UNIT}} !important; overflow: hidden !important;',
					'{{WRAPPER}}' => '--unfold-elementor-collapsed-height: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_preview_percentage',
			array(
				'label'              => esc_html__( 'Preview Height (%)', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( '%' ),
				'range'              => array(
					'%' => array( 'min' => 0, 'max' => 95, 'step' => 1 ),
				),
				'default'            => array(
					'unit' => '%',
					'size' => 35,
				),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
					'unfold_elementor_mode'   => 'content_preview',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_initially_expanded',
			array(
				'label'              => esc_html__( 'Initially Expanded', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'description'        => esc_html__( 'Leave as "No" to start in collapsed / shortened state.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_auto_hide',
			array(
				'label'              => esc_html__( 'Auto-Hide if Short', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'description'        => esc_html__( 'Automatically hides the toggle button and gradient if full content naturally fits within the collapsed height.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		// --- Animation & Transition Settings ---
		$element->add_control(
			'unfold_elementor_heading_animation',
			array(
				'label'     => esc_html__( 'Animation & Physics', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'unfold_elementor_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_animation_type',
			array(
				'label'              => esc_html__( 'Animation Effect', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'slide'      => esc_html__( 'Smooth Accordion Slide', 'unfold-for-elementor' ),
					'slide_fade' => esc_html__( 'Slide + Content Fade', 'unfold-for-elementor' ),
					'spring'     => esc_html__( 'Elastic Spring Effect', 'unfold-for-elementor' ),
					'instant'    => esc_html__( 'Instant (No Animation)', 'unfold-for-elementor' ),
				),
				'default'            => 'slide_fade',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_speed_preset',
			array(
				'label'              => esc_html__( 'Animation Speed', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'smooth' => esc_html__( 'Smooth (450ms)', 'unfold-for-elementor' ),
					'fast'   => esc_html__( 'Fast (250ms)', 'unfold-for-elementor' ),
					'slow'   => esc_html__( 'Slow & Gentle (800ms)', 'unfold-for-elementor' ),
					'custom' => esc_html__( 'Custom Duration', 'unfold-for-elementor' ),
				),
				'default'            => 'smooth',
				'condition'          => array(
					'unfold_elementor_enable'          => 'yes',
					'unfold_elementor_animation_type!' => 'instant',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_duration',
			array(
				'label'              => esc_html__( 'Custom Duration (ms)', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 50,
				'max'                => 3000,
				'step'               => 50,
				'default'            => 450,
				'condition'          => array(
					'unfold_elementor_enable'          => 'yes',
					'unfold_elementor_speed_preset'    => 'custom',
					'unfold_elementor_animation_type!' => 'instant',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_easing',
			array(
				'label'              => esc_html__( 'Easing Curve', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'cubic-bezier(0.25, 1, 0.5, 1)' => esc_html__( 'Smooth Elastic (Cubic)', 'unfold-for-elementor' ),
					'cubic-bezier(0.16, 1, 0.3, 1)' => esc_html__( 'Ultra Smooth (Expo Out)', 'unfold-for-elementor' ),
					'ease-in-out'                   => esc_html__( 'Ease In-Out', 'unfold-for-elementor' ),
					'ease-out'                      => esc_html__( 'Ease Out', 'unfold-for-elementor' ),
					'linear'                        => esc_html__( 'Linear', 'unfold-for-elementor' ),
				),
				'default'            => 'cubic-bezier(0.25, 1, 0.5, 1)',
				'condition'          => array(
					'unfold_elementor_enable'          => 'yes',
					'unfold_elementor_animation_type!' => 'instant',
				),
				'frontend_available' => true,
			)
		);

		// --- Button Controls ---
		$element->add_control(
			'unfold_elementor_heading_button',
			array(
				'label'     => esc_html__( 'Toggle Button', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'unfold_elementor_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_show_button',
			array(
				'label'              => esc_html__( 'Show Button', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_button_type',
			array(
				'label'              => esc_html__( 'Button Type / Layout', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'standard'  => esc_html__( 'Text & Icon Button', 'unfold-for-elementor' ),
					'pill'      => esc_html__( 'Pill / Rounded Button', 'unfold-for-elementor' ),
					'icon_only' => esc_html__( 'Floating Circle / Square Icon', 'unfold-for-elementor' ),
				),
				'default'            => 'standard',
				'condition'          => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_button_position',
			array(
				'label'              => esc_html__( 'Button Placement', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'below'   => esc_html__( 'Below Content (Standard)', 'unfold-for-elementor' ),
					'overlap' => esc_html__( 'Overlap Bottom Edge (Floating on Fade / Image)', 'unfold-for-elementor' ),
				),
				'default'            => 'below',
				'condition'          => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_expand_text',
			array(
				'label'              => esc_html__( 'Expand Button Text', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => esc_html__( 'Read More', 'unfold-for-elementor' ),
				'placeholder'        => esc_html__( 'Read More', 'unfold-for-elementor' ),
				'dynamic'            => array( 'active' => true ),
				'condition'          => array(
					'unfold_elementor_enable'       => 'yes',
					'unfold_elementor_show_button'  => 'yes',
					'unfold_elementor_button_type!' => 'icon_only',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_collapse_text',
			array(
				'label'              => esc_html__( 'Collapse Button Text', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => esc_html__( 'Read Less', 'unfold-for-elementor' ),
				'placeholder'        => esc_html__( 'Read Less', 'unfold-for-elementor' ),
				'dynamic'            => array( 'active' => true ),
				'condition'          => array(
					'unfold_elementor_enable'       => 'yes',
					'unfold_elementor_show_button'  => 'yes',
					'unfold_elementor_button_type!' => 'icon_only',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_show_icons',
			array(
				'label'              => esc_html__( 'Include Icons', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'condition'          => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_expand_icon',
			array(
				'label'     => esc_html__( 'Expand Icon', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
					'unfold_elementor_show_icons'  => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_collapse_icon',
			array(
				'label'     => esc_html__( 'Collapse Icon', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
					'unfold_elementor_show_icons'  => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_icon_rotate',
			array(
				'label'              => esc_html__( 'Smooth Icon Rotation (180°)', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'description'        => esc_html__( 'Smoothly animates the icon upside down on expand/collapse.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
					'unfold_elementor_show_icons'  => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_icon_position',
			array(
				'label'              => esc_html__( 'Icon Position', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
					'before' => array(
						'title' => esc_html__( 'Before Text', 'unfold-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'after'  => array(
						'title' => esc_html__( 'After Text', 'unfold-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'            => 'after',
				'condition'          => array(
					'unfold_elementor_enable'       => 'yes',
					'unfold_elementor_show_button'  => 'yes',
					'unfold_elementor_show_icons'   => 'yes',
					'unfold_elementor_button_type!' => 'icon_only',
				),
				'frontend_available' => true,
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_icon_gap',
			array(
				'label'      => esc_html__( 'Icon Gap', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button' => 'gap: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
					'unfold_elementor_show_icons'  => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_button_align',
			array(
				'label'     => esc_html__( 'Button Alignment', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'unfold-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'unfold-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'unfold-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Full Width', 'unfold-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .unfold-elementor__button-wrapper' => 'text-align: {{VALUE}} !important;',
					'{{WRAPPER}} .unfold-elementor__button-wrapper.unfold-elementor-align-justify .unfold-elementor__button' => 'width: 100% !important; justify-content: center !important;',
				),
				'condition' => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
				),
			)
		);

		// --- Advanced Behaviors ---
		$element->add_control(
			'unfold_elementor_heading_behaviors',
			array(
				'label'     => esc_html__( 'Advanced Behaviors', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'unfold_elementor_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_scroll_to',
			array(
				'label'              => esc_html__( 'Scroll to Top on Collapse', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'description'        => esc_html__( 'Smoothly scroll viewport back to element top when collapsed.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_scroll_offset',
			array(
				'label'              => esc_html__( 'Scroll Offset (px)', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => -500,
				'max'                => 500,
				'step'               => 10,
				'default'            => -60,
				'description'        => esc_html__( 'Adjust for sticky headers or admin bar (e.g. -60px).', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable'    => 'yes',
					'unfold_elementor_scroll_to' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_accordion_group',
			array(
				'label'              => esc_html__( 'Accordion Group Name', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'placeholder'        => esc_html__( 'e.g., faq-group', 'unfold-for-elementor' ),
				'description'        => esc_html__( 'Give multiple folded elements the same group name to automatically collapse others when one expands.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_remember_state',
			array(
				'label'              => esc_html__( 'Remember State Across Reloads', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'description'        => esc_html__( 'Saves user expand/collapse preference in browser localStorage.', 'unfold-for-elementor' ),
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_disable_desktop',
			array(
				'label'              => esc_html__( 'Disable on Desktop', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_disable_tablet',
			array(
				'label'              => esc_html__( 'Disable on Tablet', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_disable_mobile',
			array(
				'label'              => esc_html__( 'Disable on Mobile', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'no',
				'condition'          => array(
					'unfold_elementor_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Register Dynamic Overlay Section in Advanced Tab.
	 *
	 * @param Element_Base $element
	 */
	protected function register_overlay_section( $element ) {
		$element->start_controls_section(
			'section_unfold_elementor_overlay',
			array(
				'label'     => esc_html__( 'Unfold: Dynamic Overlay', 'unfold-for-elementor' ),
				'tab'       => Controls_Manager::TAB_ADVANCED,
				'condition' => array(
					'unfold_elementor_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_fade_enable',
			array(
				'label'              => esc_html__( 'Enable Overlay', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'unfold-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'unfold-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'unfold_elementor_overlay_style',
			array(
				'label'              => esc_html__( 'Overlay Style', 'unfold-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'classic'     => esc_html__( 'Classic Bottom Gradient', 'unfold-for-elementor' ),
					'frosted'     => esc_html__( 'Frosted Glass (Blur & Gradient)', 'unfold-for-elementor' ),
					'card_dark'   => esc_html__( 'Dark Card Shadow / Vignette', 'unfold-for-elementor' ),
					'custom_grad' => esc_html__( 'Custom Background Stops', 'unfold-for-elementor' ),
				),
				'default'            => 'classic',
				'condition'          => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_fade_enable' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_fade_height',
			array(
				'label'      => esc_html__( 'Overlay Height', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem', 'vh' ),
				'range'      => array(
					'px'  => array( 'min' => 10, 'max' => 500 ),
					'%'   => array( 'min' => 10, 'max' => 100 ),
					'rem' => array( 'min' => 1, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__fade' => 'height: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_fade_enable' => 'yes',
				),
			)
		);

		$element->add_control(
			'unfold_elementor_fade_color',
			array(
				'label'     => esc_html__( 'Overlay Base Color', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .unfold-elementor__fade.unfold-elementor__fade--classic' => 'background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, {{VALUE}} 100%) !important;',
					'{{WRAPPER}} .unfold-elementor__fade.unfold-elementor__fade--frosted' => 'background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, {{VALUE}} 95%) !important; backdrop-filter: blur(8px) !important; -webkit-backdrop-filter: blur(8px) !important;',
				),
				'condition' => array(
					'unfold_elementor_enable'        => 'yes',
					'unfold_elementor_fade_enable'   => 'yes',
					'unfold_elementor_overlay_style' => array( 'classic', 'frosted' ),
				),
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'unfold_elementor_custom_overlay_bg',
				'label'     => esc_html__( 'Custom Overlay Background', 'unfold-for-elementor' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .unfold-elementor__fade.unfold-elementor__fade--custom_grad',
				'condition' => array(
					'unfold_elementor_enable'        => 'yes',
					'unfold_elementor_fade_enable'   => 'yes',
					'unfold_elementor_overlay_style' => 'custom_grad',
				),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Register Button Style Section in Advanced Tab.
	 *
	 * @param Element_Base $element
	 */
	protected function register_style_section( $element ) {
		$element->start_controls_section(
			'section_unfold_elementor_button_style',
			array(
				'label'     => esc_html__( 'Unfold: Button Style', 'unfold-for-elementor' ),
				'tab'       => Controls_Manager::TAB_ADVANCED,
				'condition' => array(
					'unfold_elementor_enable'      => 'yes',
					'unfold_elementor_show_button' => 'yes',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'unfold_elementor_btn_typography',
				'label'     => esc_html__( 'Typography', 'unfold-for-elementor' ),
				'selector'  => '{{WRAPPER}} .unfold-elementor__button',
				'condition' => array(
					'unfold_elementor_button_type!' => 'icon_only',
				),
			)
		);

		$element->start_controls_tabs( 'unfold_elementor_btn_tabs' );

		// --- Normal State ---
		$element->start_controls_tab(
			'unfold_elementor_btn_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'unfold-for-elementor' ),
			)
		);

		$element->add_control(
			'unfold_elementor_btn_text_color',
			array(
				'label'     => esc_html__( 'Text & Icon Color', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'selectors' => array(
					'{{WRAPPER}} .unfold-elementor__button' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .unfold-elementor__button svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .unfold-elementor__button svg path' => 'fill: {{VALUE}} !important;',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'unfold_elementor_btn_bg',
				'label'    => esc_html__( 'Background', 'unfold-for-elementor' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .unfold-elementor__button',
			)
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'unfold_elementor_btn_border',
				'label'    => esc_html__( 'Border', 'unfold-for-elementor' ),
				'selector' => '{{WRAPPER}} .unfold-elementor__button',
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'unfold_elementor_btn_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'unfold-for-elementor' ),
				'selector' => '{{WRAPPER}} .unfold-elementor__button',
			)
		);

		$element->end_controls_tab();

		// --- Hover State ---
		$element->start_controls_tab(
			'unfold_elementor_btn_tab_hover',
			array(
				'label' => esc_html__( 'Hover', 'unfold-for-elementor' ),
			)
		);

		$element->add_control(
			'unfold_elementor_btn_text_color_hover',
			array(
				'label'     => esc_html__( 'Text & Icon Color', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .unfold-elementor__button:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .unfold-elementor__button:hover svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .unfold-elementor__button:hover svg path' => 'fill: {{VALUE}} !important;',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'unfold_elementor_btn_bg_hover',
				'label'    => esc_html__( 'Background', 'unfold-for-elementor' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .unfold-elementor__button:hover',
			)
		);

		$element->add_control(
			'unfold_elementor_btn_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'unfold-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .unfold-elementor__button:hover' => 'border-color: {{VALUE}} !important;',
				),
				'condition' => array(
					'unfold_elementor_btn_border_border!' => '',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'unfold_elementor_btn_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'unfold-for-elementor' ),
				'selector' => '{{WRAPPER}} .unfold-elementor__button:hover',
			)
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();

		// --- Layout & Dimensions ---
		$element->add_responsive_control(
			'unfold_elementor_btn_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_btn_padding',
			array(
				'label'      => esc_html__( 'Padding', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 16,
					'bottom'   => 8,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_btn_size_dimensions',
			array(
				'label'      => esc_html__( 'Button Width / Size (for Icon/Pill)', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 200 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button--icon_only' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'unfold_elementor_button_type' => 'icon_only',
				),
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_btn_wrapper_margin',
			array(
				'label'      => esc_html__( 'Button Wrapper Margin / Offset', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$element->add_responsive_control(
			'unfold_elementor_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'unfold-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 6, 'max' => 50 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .unfold-elementor__button i'   => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .unfold-elementor__button svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'unfold_elementor_show_icons' => 'yes',
				),
			)
		);

		$element->end_controls_section();
	}
}
