/**
 * Unfold for Elementor - Editor Live Preview Handler
 *
 * Real-time synchronization when modifying Unfold controls in Elementor panel.
 *
 * @package Dynime\UnfoldElementor
 */

(function ($) {
  'use strict';

  function getPreviewUnfold() {
    if (window.elementor && window.elementor.$preview && window.elementor.$preview[0]) {
      var iframeWin = window.elementor.$preview[0].contentWindow;
      if (iframeWin && iframeWin.UnfoldElementor) {
        return iframeWin.UnfoldElementor;
      }
    }
    return window.UnfoldElementor || null;
  }

  function getPreviewElement(elementView) {
    if (!elementView) return null;
    if (window.elementor && window.elementor.$previewContents) {
      return window.elementor.$previewContents.find(elementView.el);
    }
    return $(elementView.el);
  }

  function buildConfigFromSettings(settings, elementId) {
    var heightObj = settings.unfold_elementor_collapsed_height || {};
    var heightTabletObj = settings.unfold_elementor_collapsed_height_tablet || {};
    var heightMobileObj = settings.unfold_elementor_collapsed_height_mobile || {};

    var animType = settings.unfold_elementor_animation_type || 'slide_fade';
    var speedPreset = settings.unfold_elementor_speed_preset || 'smooth';
    var duration = 450;
    if (animType === 'instant') duration = 0;
    else if (speedPreset === 'fast') duration = 250;
    else if (speedPreset === 'slow') duration = 800;
    else if (speedPreset === 'custom') duration = parseInt(settings.unfold_elementor_duration, 10) || 450;

    return {
      id: elementId,
      mode: settings.unfold_elementor_mode || 'height',
      initially_expanded: settings.unfold_elementor_initially_expanded === 'yes',
      auto_hide: settings.unfold_elementor_auto_hide !== 'no',
      height: {
        desktop: heightObj.size !== undefined ? parseFloat(heightObj.size) : 160,
        tablet: heightTabletObj.size !== undefined ? parseFloat(heightTabletObj.size) : 140,
        mobile: heightMobileObj.size !== undefined ? parseFloat(heightMobileObj.size) : 120,
        unit: heightObj.unit || 'px',
      },
      preview_percentage: settings.unfold_elementor_preview_percentage && settings.unfold_elementor_preview_percentage.size !== undefined
        ? parseFloat(settings.unfold_elementor_preview_percentage.size)
        : 35,
      animation_type: animType,
      duration: duration,
      easing: settings.unfold_elementor_easing || 'cubic-bezier(0.25, 1, 0.5, 1)',
      show_button: settings.unfold_elementor_show_button !== 'no',
      button_type: settings.unfold_elementor_button_type || 'standard',
      button_position: settings.unfold_elementor_button_position || 'below',
      expand_text: settings.unfold_elementor_expand_text || 'Read More',
      collapse_text: settings.unfold_elementor_collapse_text || 'Read Less',
      show_icons: settings.unfold_elementor_show_icons !== 'no',
      icon_rotate: settings.unfold_elementor_icon_rotate !== 'no',
      icon_position: settings.unfold_elementor_icon_position || 'after',
      fade_enable: settings.unfold_elementor_fade_enable !== 'no',
      overlay_style: settings.unfold_elementor_overlay_style || 'classic',
      scroll_to: settings.unfold_elementor_scroll_to === 'yes',
      scroll_offset: parseInt(settings.unfold_elementor_scroll_offset, 10) || -60,
      accordion_group: settings.unfold_elementor_accordion_group || '',
      remember_state: settings.unfold_elementor_remember_state === 'yes',
      disable_desktop: settings.unfold_elementor_disable_desktop === 'yes',
      disable_tablet: settings.unfold_elementor_disable_tablet === 'yes',
      disable_mobile: settings.unfold_elementor_disable_mobile === 'yes',
    };
  }

  function handleControlChange(controlView, elementView) {
    if (!controlView || !controlView.model || !elementView || !elementView.model) return;

    var controlName = controlView.model.get('name');
    if (!controlName || controlName.indexOf('unfold_elementor_') !== 0) return;

    var settingsModel = elementView.model.get('settings');
    if (!settingsModel) return;

    var settings = settingsModel.toJSON();
    var $el = getPreviewElement(elementView);
    if (!$el || !$el.length) return;

    var Unfold = getPreviewUnfold();
    if (!Unfold) return;

    if (settings.unfold_elementor_enable === 'yes') {
      var elementId = elementView.model.get('id') || $el.data('id');
      var config = buildConfigFromSettings(settings, elementId);
      $el.attr('data-unfold-elementor', JSON.stringify(config));
      Unfold.initElement($el, config);
    } else {
      Unfold.destroyElement($el);
    }
  }

  $(window).on('elementor:init', function () {
    if (!window.elementor) return;

    // Listen to editor live control changes
    if (elementor.channels && elementor.channels.editor) {
      elementor.channels.editor.on('change', handleControlChange);
    }

    // Panel open hooks
    elementor.hooks.addAction('panel/open_editor/common', function (panel, model, view) {
      if (!model || !model.get('settings')) return;
      var settings = model.get('settings').toJSON();
      if (settings.unfold_elementor_enable === 'yes') {
        var $el = getPreviewElement(view);
        var Unfold = getPreviewUnfold();
        if ($el && Unfold) {
          var config = buildConfigFromSettings(settings, model.get('id') || $el.data('id'));
          $el.attr('data-unfold-elementor', JSON.stringify(config));
          Unfold.initElement($el, config);
        }
      }
    });

    elementor.hooks.addAction('panel/open_editor/container', function (panel, model, view) {
      if (!model || !model.get('settings')) return;
      var settings = model.get('settings').toJSON();
      if (settings.unfold_elementor_enable === 'yes') {
        var $el = getPreviewElement(view);
        var Unfold = getPreviewUnfold();
        if ($el && Unfold) {
          var config = buildConfigFromSettings(settings, model.get('id') || $el.data('id'));
          $el.attr('data-unfold-elementor', JSON.stringify(config));
          Unfold.initElement($el, config);
        }
      }
    });
  });

})(jQuery);
