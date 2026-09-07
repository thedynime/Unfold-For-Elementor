/**
 * Unfold for Elementor - Frontend Engine
 *
 * Universal Expand / Collapse Handler for Elementor Elements
 *
 * @package Dynime\UnfoldElementor
 */

(function ($) {
  'use strict';

  var UnfoldElementor = {
    storagePrefix: 'unfold_elementor_state_',

    /**
     * Get current responsive device mode
     */
    getCurrentDevice: function () {
      var width = window.innerWidth;
      if (width >= 1025) {
        return 'desktop';
      } else if (width >= 768) {
        return 'tablet';
      } else {
        return 'mobile';
      }
    },

    /**
     * Extract or create inner target container safely
     */
    getInnerElement: function ($el) {
      if ($el.find('> .unfold-elementor__inner').length) {
        return $el.find('> .unfold-elementor__inner');
      }
      if ($el.find('> .e-con-inner').length) {
        var $conInner = $el.find('> .e-con-inner');
        $conInner.addClass('unfold-elementor__inner');
        return $conInner;
      }
      if ($el.find('> .elementor-container').length) {
        var $secInner = $el.find('> .elementor-container');
        $secInner.addClass('unfold-elementor__inner');
        return $secInner;
      }
      if ($el.find('> .elementor-widget-container').length) {
        var $widgetInner = $el.find('> .elementor-widget-container');
        $widgetInner.addClass('unfold-elementor__inner');
        return $widgetInner;
      }
      if ($el.find('> .elementor-widget-wrap').length) {
        var $colInner = $el.find('> .elementor-widget-wrap');
        $colInner.addClass('unfold-elementor__inner');
        return $colInner;
      }

      // If no standard inner container, wrap non-UI direct children
      var $children = $el.children(':not(.unfold-elementor__fade):not(.unfold-elementor__button-wrapper):not(.elementor-editor-element-settings):not(.elementor-editor-container-settings):not(.elementor-element-overlay)');
      if ($children.length) {
        $children.wrapAll('<div class="unfold-elementor__inner"></div>');
        return $el.find('> .unfold-elementor__inner');
      }

      return $el;
    },

    /**
     * Get target collapsed height for current device (supports 0px)
     */
    getCollapsedHeight: function ($el, config, naturalHeight) {
      var device = this.getCurrentDevice();
      var heightObj = config.height || {};
      var rawHeight = heightObj[device] !== undefined ? heightObj[device] : (heightObj.desktop !== undefined ? heightObj.desktop : 160);
      var height = parseFloat(rawHeight);
      if (isNaN(height)) {
        height = 160;
      }

      var unit = heightObj.unit || 'px';

      if (config.mode === 'content_preview') {
        var percentage = config.preview_percentage !== undefined ? parseFloat(config.preview_percentage) : 35;
        return (naturalHeight * percentage) / 100;
      }

      if (unit === 'vh') {
        return (window.innerHeight * height) / 100;
      } else if (unit === 'rem') {
        var rootFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
        return height * rootFontSize;
      } else if (unit === '%') {
        return (naturalHeight * height) / 100;
      }

      return height;
    },

    /**
     * Clean up an element completely if Unfold is disabled
     */
    destroyElement: function ($el) {
      if (!$el || !$el.length) return;
      var instance = $el.data('unfold-elementor-instance');
      if (instance && instance.resizeObserver) {
        instance.resizeObserver.disconnect();
      }

      $el.find('.unfold-elementor__fade').remove();
      $el.find('> .unfold-elementor__button-wrapper').remove();
      var $inner = this.getInnerElement($el);
      $inner.css({ 'max-height': '', 'overflow': '', 'transition': '', 'opacity': '' });
      $el.css({ '--unfold-elementor-collapsed-height': '' });

      $el.removeClass('unfold-elementor-container unfold-elementor--active unfold-elementor--collapsed unfold-elementor--expanded unfold-elementor--initialized unfold-elementor--content-fits unfold-elementor--animating unfold-elementor--zero-height unfold-elementor--icon-rotate');
      $el.removeData('unfold-elementor-instance');
      $el.removeAttr('data-unfold-elementor');
    },

    /**
     * Initialize or re-initialize a single element
     */
    initElement: function ($el, customConfig) {
      if (!$el || !$el.length) return;

      var config = customConfig;
      if (!config) {
        var rawData = $el.attr('data-unfold-elementor');
        if (rawData) {
          try {
            config = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
          } catch (e) {
            console.error('Unfold for Elementor: Invalid JSON config', e);
            return;
          }
        }
      }

      if (!config) return;

      var self = this;
      var elementId = config.id || $el.data('id') || Math.random().toString(36).substr(2, 9);

      // Check device disabling
      var device = self.getCurrentDevice();
      if (
        (device === 'desktop' && config.disable_desktop) ||
        (device === 'tablet' && config.disable_tablet) ||
        (device === 'mobile' && config.disable_mobile)
      ) {
        $el.addClass('unfold-elementor--disabled-' + device);
        return;
      } else {
        $el.removeClass('unfold-elementor--disabled-desktop unfold-elementor--disabled-tablet unfold-elementor--disabled-mobile');
      }

      // Add wrapper classes
      $el.addClass('unfold-elementor-container unfold-elementor--active');

      if (config.button_position === 'overlap') {
        $el.addClass('unfold-elementor--btn-pos-overlap');
      } else {
        $el.removeClass('unfold-elementor--btn-pos-overlap');
      }

      if (config.icon_rotate) {
        $el.addClass('unfold-elementor--icon-rotate');
      } else {
        $el.removeClass('unfold-elementor--icon-rotate');
      }

      // Identify inner container
      var $inner = self.getInnerElement($el);

      // Unique ID for accessibility aria-controls
      var contentDomId = 'unfold-content-' + elementId;
      $inner.attr('id', contentDomId);

      // Append / update Fade Overlay inside inner container
      var overlayStyle = config.overlay_style || 'classic';
      var $fade = $inner.find('> .unfold-elementor__fade');
      if (config.fade_enable) {
        if (!$fade.length) {
          $fade = $('<div class="unfold-elementor__fade" aria-hidden="true"></div>');
          $inner.append($fade);
        }
        $fade.removeClass('unfold-elementor__fade--classic unfold-elementor__fade--frosted unfold-elementor__fade--card_dark unfold-elementor__fade--custom_grad');
        $fade.addClass('unfold-elementor__fade--' + overlayStyle);
      } else {
        $fade.remove();
        $fade = null;
      }

      // Build or update Button outside inner container
      var $btnWrapper = $el.find('> .unfold-elementor__button-wrapper');
      var $button;

      if (config.show_button) {
        if (!$btnWrapper.length) {
          $btnWrapper = $('<div class="unfold-elementor__button-wrapper"></div>');
          $el.append($btnWrapper);
        }

        var btnType = config.button_type || 'standard';
        var iconPosClass = config.icon_position === 'before' ? 'unfold-elementor__button--icon-before' : 'unfold-elementor__button--icon-after';
        var btnTypeClass = 'unfold-elementor__button--' + btnType;
        
        $btnWrapper.empty();

        $button = $(
          '<button type="button" class="unfold-elementor__button ' + iconPosClass + ' ' + btnTypeClass + '" aria-expanded="false" aria-controls="' + contentDomId + '"></button>'
        );

        if (btnType !== 'icon_only') {
          $button.append('<span class="unfold-elementor__text">' + (config.expand_text || 'Read More') + '</span>');
        } else {
          $button.attr('aria-label', config.expand_text || 'Read More');
        }

        if (config.show_icons) {
          var defaultDownSvg = '<svg viewBox="0 0 448 512" width="14" height="14"><path fill="currentColor" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>';
          var defaultUpSvg = '<svg viewBox="0 0 448 512" width="14" height="14"><path fill="currentColor" d="M240.971 130.524l194.343 194.343c9.373 9.373 9.373 24.569 0 33.941l-22.667 22.667c-9.357 9.357-24.522 9.375-33.901.04L224 227.495 69.255 381.516c-9.379 9.335-24.544 9.317-33.901-.04l-22.667-22.667c-9.373-9.373-9.373-24.569 0-33.941L207.03 130.524c9.372-9.373 24.568-9.373 33.941 0z"></path></svg>';

          var $expandIcon = $(config.expand_icon_html || '<span class="unfold-elementor__icon unfold-elementor__icon--expand">' + defaultDownSvg + '</span>');
          var $collapseIcon = $(config.collapse_icon_html || '<span class="unfold-elementor__icon unfold-elementor__icon--collapse">' + defaultUpSvg + '</span>');

          if (config.icon_rotate) {
            $button.append($expandIcon);
          } else {
            if (config.icon_position === 'before') {
              $button.prepend($collapseIcon).prepend($expandIcon);
            } else {
              $button.append($expandIcon).append($collapseIcon);
            }
          }
        }

        $btnWrapper.append($button);
      } else {
        $btnWrapper.remove();
        $btnWrapper = null;
        $button = null;
      }

      // Check saved state or initially expanded
      var isExpanded = Boolean(config.initially_expanded);
      if (config.remember_state) {
        var savedState = localStorage.getItem(self.storagePrefix + elementId);
        if (savedState !== null) {
          isExpanded = savedState === 'expanded';
        }
      }

      // Cleanup existing observer
      var existing = $el.data('unfold-elementor-instance');
      if (existing && existing.resizeObserver) {
        existing.resizeObserver.disconnect();
      }

      var instance = {
        $el: $el,
        $inner: $inner,
        $fade: $fade,
        $button: $button,
        $btnWrapper: $btnWrapper,
        config: config,
        elementId: elementId,
        isExpanded: isExpanded,
        isAnimating: false,
      };

      $el.data('unfold-elementor-instance', instance);
      $el.addClass('unfold-elementor--initialized');

      // Update state and apply height
      self.updateElementState(instance, false);

      // Bind button events
      if ($button && $button.length) {
        $button.off('click.unfoldElementor').on('click.unfoldElementor', function (e) {
          e.preventDefault();
          e.stopPropagation();
          self.toggle(instance);
        });

        $button.off('keydown.unfoldElementor').on('keydown.unfoldElementor', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            e.stopPropagation();
            self.toggle(instance);
          }
        });
      }

      // ResizeObserver
      if (window.ResizeObserver && $inner[0]) {
        var resizeObserver = new ResizeObserver(function () {
          if (!instance.isAnimating) {
            self.recalculate(instance);
          }
        });
        resizeObserver.observe($inner[0]);
        instance.resizeObserver = resizeObserver;
      }
    },

    /**
     * Recalculate dimensions and auto-hide
     */
    recalculate: function (instance) {
      if (!instance || !instance.$inner || !instance.$inner.length) return;
      var self = this;
      var naturalHeight = instance.$inner[0].scrollHeight;
      var collapsedHeight = self.getCollapsedHeight(instance.$el, instance.config, naturalHeight);

      // Auto-hide only if collapsed height > 10 and natural height fits
      if (instance.config.auto_hide && collapsedHeight > 10 && naturalHeight <= (collapsedHeight + 5)) {
        instance.$el.addClass('unfold-elementor--content-fits');
        if (instance.$btnWrapper) instance.$btnWrapper.hide();
        if (instance.$fade) instance.$fade.hide();
        instance.$inner.css({ 'max-height': 'none', 'overflow': 'visible', 'opacity': '1' });
      } else {
        instance.$el.removeClass('unfold-elementor--content-fits');
        if (instance.$btnWrapper) instance.$btnWrapper.show();
        if (instance.$fade) instance.$fade.show();

        if (!instance.isExpanded) {
          instance.$el.css('--unfold-elementor-collapsed-height', collapsedHeight + 'px');
          if (collapsedHeight === 0) {
            instance.$el.addClass('unfold-elementor--zero-height');
            instance.$inner.css({ 'max-height': '0px', 'overflow': 'hidden', 'opacity': '0' });
          } else {
            instance.$el.removeClass('unfold-elementor--zero-height');
            instance.$inner.css({ 'max-height': collapsedHeight + 'px', 'overflow': 'hidden', 'opacity': '1' });
          }
        }
      }
    },

    /**
     * Update classes, button text, ARIA attributes
     */
    updateElementState: function (instance, animate) {
      var self = this;
      var config = instance.config;
      var $inner = instance.$inner;
      var $el = instance.$el;
      var $button = instance.$button;
      var naturalHeight = $inner[0] ? $inner[0].scrollHeight : 0;
      var collapsedHeight = self.getCollapsedHeight($el, config, naturalHeight);

      // Auto-hide check (only if collapsedHeight > 10)
      if (config.auto_hide && collapsedHeight > 10 && naturalHeight <= (collapsedHeight + 5)) {
        $el.addClass('unfold-elementor--content-fits');
        if (instance.$btnWrapper) instance.$btnWrapper.hide();
        if (instance.$fade) instance.$fade.hide();
        $inner.css({ 'max-height': 'none', 'overflow': 'visible', 'opacity': '1' });
        return;
      }

      $el.removeClass('unfold-elementor--content-fits');
      if (instance.$btnWrapper) instance.$btnWrapper.show();
      if (instance.$fade) instance.$fade.show();

      $el.css('--unfold-elementor-collapsed-height', collapsedHeight + 'px');

      var duration = config.duration !== undefined ? config.duration : 450;
      var easing = config.easing || 'cubic-bezier(0.25, 1, 0.5, 1)';
      var animType = config.animation_type || 'slide_fade';

      if (instance.isExpanded) {
        $el.removeClass('unfold-elementor--collapsed unfold-elementor--zero-height').addClass('unfold-elementor--expanded');
        
        if ($button && $button.length) {
          $button.attr('aria-expanded', 'true');
          if (config.button_type !== 'icon_only') {
            $button.find('.unfold-elementor__text').text(config.collapse_text || 'Read Less');
          } else {
            $button.attr('aria-label', config.collapse_text || 'Read Less');
          }
          if (config.icon_rotate) {
            $button.find('.unfold-elementor__icon').css('transform', 'rotate(180deg)');
          }
        }

        if (instance.$fade) {
          instance.$fade.css({ opacity: 0, visibility: 'hidden' });
        }

        if (animate && duration > 0) {
          instance.isAnimating = true;
          $el.addClass('unfold-elementor--animating');
          
          var animTransition = 'max-height ' + duration + 'ms ' + easing;
          if (animType === 'slide_fade') {
            animTransition += ', opacity ' + duration + 'ms ease';
          }

          $inner.css({
            'transition': animTransition,
            'max-height': naturalHeight + 'px',
            'opacity': '1',
            'overflow': 'hidden',
          });

          setTimeout(function () {
            if (instance.isExpanded) {
              $inner.css({ 'max-height': 'none', 'overflow': 'visible' });
            }
            $el.removeClass('unfold-elementor--animating');
            instance.isAnimating = false;
          }, duration + 30);
        } else {
          $inner.css({ 'max-height': 'none', 'overflow': 'visible', 'opacity': '1', 'transition': 'none' });
        }
      } else {
        $el.removeClass('unfold-elementor--expanded').addClass('unfold-elementor--collapsed');
        if (collapsedHeight === 0) {
          $el.addClass('unfold-elementor--zero-height');
        } else {
          $el.removeClass('unfold-elementor--zero-height');
        }

        if ($button && $button.length) {
          $button.attr('aria-expanded', 'false');
          if (config.button_type !== 'icon_only') {
            $button.find('.unfold-elementor__text').text(config.expand_text || 'Read More');
          } else {
            $button.attr('aria-label', config.expand_text || 'Read More');
          }
          if (config.icon_rotate) {
            $button.find('.unfold-elementor__icon').css('transform', 'rotate(0deg)');
          }
        }

        if (instance.$fade && collapsedHeight > 0) {
          instance.$fade.css({ opacity: 1, visibility: 'visible' });
        }

        if (animate && duration > 0) {
          instance.isAnimating = true;
          $el.addClass('unfold-elementor--animating');
          
          var startHeight = $inner[0].scrollHeight;
          $inner.css({ 'max-height': startHeight + 'px', 'overflow': 'hidden' });
          $inner[0].offsetHeight; // Force reflow

          var animCollapseTransition = 'max-height ' + duration + 'ms ' + easing;
          if (animType === 'slide_fade' && collapsedHeight === 0) {
            animCollapseTransition += ', opacity ' + duration + 'ms ease';
          }

          $inner.css({
            'transition': animCollapseTransition,
            'max-height': collapsedHeight + 'px',
            'opacity': collapsedHeight === 0 ? '0' : '1',
          });

          setTimeout(function () {
            $el.removeClass('unfold-elementor--animating');
            instance.isAnimating = false;
          }, duration + 30);
        } else {
          $inner.css({
            'max-height': collapsedHeight + 'px',
            'opacity': collapsedHeight === 0 ? '0' : '1',
            'overflow': 'hidden',
            'transition': 'none',
          });
        }
      }
    },

    /**
     * Toggle expanded / collapsed state
     */
    toggle: function (instance) {
      if (instance.isAnimating) return;
      var self = this;

      if (!instance.isExpanded) {
        // Handle Accordion Group
        if (instance.config.accordion_group) {
          var groupName = instance.config.accordion_group;
          $('.unfold-elementor-container.unfold-elementor--expanded').each(function () {
            var otherInst = $(this).data('unfold-elementor-instance');
            if (otherInst && otherInst !== instance && otherInst.config.accordion_group === groupName) {
              otherInst.isExpanded = false;
              self.updateElementState(otherInst, true);
              if (otherInst.config.remember_state) {
                localStorage.setItem(self.storagePrefix + otherInst.elementId, 'collapsed');
              }
            }
          });
        }

        instance.isExpanded = true;
        self.updateElementState(instance, true);

        if (instance.config.remember_state) {
          localStorage.setItem(self.storagePrefix + instance.elementId, 'expanded');
        }
      } else {
        instance.isExpanded = false;
        self.updateElementState(instance, true);

        if (instance.config.remember_state) {
          localStorage.setItem(self.storagePrefix + instance.elementId, 'collapsed');
        }

        // Scroll to Top on Collapse
        if (instance.config.scroll_to) {
          var offset = instance.config.scroll_offset || -60;
          var targetTop = instance.$el.offset().top + offset;
          $('html, body').animate({ scrollTop: targetTop }, Math.max(instance.config.duration || 450, 300));
        }
      }
    },

    /**
     * Initialize all elements in given container
     */
    init: function ($scope) {
      var self = this;
      var $elements;

      if ($scope && $scope.length) {
        $elements = $scope.hasClass('unfold-elementor-container') || $scope.is('[data-unfold-elementor]')
          ? $scope
          : $scope.find('.unfold-elementor-container, [data-unfold-elementor]');
      } else {
        $elements = $('.unfold-elementor-container, [data-unfold-elementor]');
      }

      $elements.each(function () {
        self.initElement($(this));
      });
    },
  };

  window.UnfoldElementor = UnfoldElementor;

  // Initialize on Elementor Frontend Ready
  $(window).on('elementor/frontend/init', function () {
    if (window.elementorFrontend && elementorFrontend.hooks) {
      elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
        UnfoldElementor.init($scope);
      });
    }
  });

  // Fallback for standard DOM ready
  $(document).ready(function () {
    UnfoldElementor.init($(document));
  });

  // Window resize handler
  var resizeTimer;
  $(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      $('.unfold-elementor-container.unfold-elementor--initialized').each(function () {
        var inst = $(this).data('unfold-elementor-instance');
        if (inst) {
          UnfoldElementor.recalculate(inst);
        }
      });
    }, 150);
  });

})(jQuery);
