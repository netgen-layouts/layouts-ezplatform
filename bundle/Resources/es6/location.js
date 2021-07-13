import $ from 'jquery';
import NlModal from './NlModal';

/* eslint-disable prefer-arrow-callback */
/* eslint-disable func-names */
$(function () {
  /* edit layout box */
  function LayoutMapped(el, layouts) {
    this.$el = $(el);
    this.layouts = layouts;
    this.id = el.dataset.layoutId;
    this.$layoutCacheModal = this.$el.find('.layout-cache-modal');
    this.setupEvents();
  }

  LayoutMapped.prototype.setupEvents = function () {
    this.$el.find('.js-clear-layout-cache').on('click', this.openCacheModal.bind(this));
    this.$el.find('.js-clear-block-caches').on('click', this.clearBlockCaches.bind(this));
    this.$layoutCacheModal.on('click', '.js-modal-confirm', this.clearLayoutCache.bind(this));
  };

  LayoutMapped.prototype.openCacheModal = function (e) {
    e.preventDefault();
    this.$layoutCacheModal.find('.errors').remove();
    this.cacheModalStopLoading();
    this.$layoutCacheModal.modal('show');
  };

  LayoutMapped.prototype.cacheModalStartLoading = function () {
    if (!this.$layoutCacheModal.find('.modal-loading').length) this.$layoutCacheModal.find('.modal-body').append('<div class="modal-loading"><i class="loading-ng-icon"></i></div>');
  };

  LayoutMapped.prototype.cacheModalStopLoading = function () {
    this.$layoutCacheModal.find('.modal-loading').remove();
  };

  LayoutMapped.prototype.clearLayoutCache = function (e) {
    e.preventDefault();
    const self = this;

    $.ajax({
      type: 'POST',
      url: `${this.layouts.basePath}${this.id}/cache`,
      headers: {
        'X-CSRF-Token': this.layouts.csrf,
      },
      beforeSend() {
        self.$layoutCacheModal.find('.errors').remove();
        self.cacheModalStartLoading();
      },
      success() {
        self.$layoutCacheModal.modal('hide');
      },
      error(xhr) {
        const $resp = $(xhr.responseText);
        self.$layoutCacheModal.find('.modal-body').prepend($resp.find('.errors'));
        self.cacheModalStopLoading();
      },
    });
  };

  LayoutMapped.prototype.indeterminateCheckboxes = function ($form) {
    const $checkboxes = [];
    const $submit = $form.find('button[type="submit"]');

    const changeState = function (arr) {
      let checkedNr = 0;

      arr.forEach(function (el) {
        return el.checked && checkedNr++;
      });

      $('input[type="checkbox"]#toggle-all-cache').prop({
        indeterminate: checkedNr > 0 && checkedNr < arr.length,
        checked: checkedNr === arr.length,
      });

      $submit.prop('disabled', checkedNr === 0);
    };

    $form.find('input[type="checkbox"]').each(function (i, el) {
      el.id !== 'toggle-all-cache' && $checkboxes.push(el);
    });

    changeState($checkboxes);

    $form.on('change', 'input[type="checkbox"]', function (e) {
      if (e.currentTarget.id === 'toggle-all-cache') {
        $checkboxes.forEach(function (el) {
          $(el).prop('checked', e.currentTarget.checked);
        });

        $submit.prop('disabled', !e.currentTarget.checked);
      } else {
        changeState($checkboxes);
      }
    });
  };

  LayoutMapped.prototype.clearBlockCaches = function (e) {
    e.preventDefault();
    const self = this;

    const afterModalRender = function ($form) {
      $form.find('.nl-btn').addClass('btn btn-primary');
      self.layouts.$blockCacheModal.find('.modal-title').html($form.find('.nl-modal-head'));
      self.indeterminateCheckboxes($form);
    };

    const formAction = function (el) {
      el.preventDefault();
      const $form = $(el.currentTarget);
      $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: $form.serialize(),
        headers: {
          'X-CSRF-Token': self.layouts.csrf,
        },
        beforeSend() {
          self.layouts.cacheModalStartLoading();
        },
        success() {
          self.layouts.$blockCacheModal.modal('hide');
        },
        error(xhr) {
          self.layouts.cacheModalStopLoading();
          $form.html(xhr.responseText);
          afterModalRender($form);
        },
      });
    };

    this.layouts.cacheModalStartLoading();
    this.layouts.$blockCacheModal.modal('show');

    $.ajax({
      type: 'GET',
      url: `${this.layouts.basePath}${this.id}/cache/blocks`,
      success(data) {
        const $form = $(data);
        afterModalRender($form);
        self.layouts.$blockCacheModal.find('.modal-body').html($form);
        $form.on('submit', formAction.bind(this));
      },
      error(xhr) {
        self.layouts.$blockCacheModal.find('.modal-body').html(xhr.responseText);
        self.layouts.cacheModalStopLoading();
      },
    });
  };

  function LayoutsBox(el) {
    this.$el = $(el);
    this.csrf = $('meta[name=nglayouts-admin-csrf-token]').attr('content');
    this.basePath = $('meta[name=nglayouts-admin-base-path]').attr('content');
    this.basePath += this.basePath.charAt(this.basePath.length - 1) !== '/' ? '/layouts/' : 'layouts/';
    this.$content = this.$el.find('.layouts-box-content');
    this.$loader = this.$el.find('.layout-loading');
    this.fetchedLayouts = false;
    this.$toggleBtn = $('a[href^="#ez-tab-location-view-netgen_layouts"]');
    this.url = el.dataset.url;
    this.setupEvents();
    this.$el.is(':visible') && this.getLayouts();
  }

  LayoutsBox.prototype.setupEvents = function () {
    this.$toggleBtn.on('click', this.getLayouts.bind(this));
  };

  LayoutsBox.prototype.initLayouts = function () {
    const self = this;
    this.$el.find('.layout-list-item').each(function () {
      return new LayoutMapped(this, self);
    });
  };

  LayoutsBox.prototype.cacheModalStartLoading = function () {
    if (!this.$blockCacheModal.find('.modal-loading').length) {
      this.$blockCacheModal.find('.modal-title').html('&nbsp;');
      this.$blockCacheModal.find('.modal-body').append('<div class="modal-loading"><i class="loading-ng-icon"></i></div>');
    }
  };

  LayoutsBox.prototype.cacheModalStopLoading = function () {
    this.$blockCacheModal.find('.modal-loading').remove();
  };

  LayoutsBox.prototype.showLoader = function () {
    this.$el.addClass('loading');
  };

  LayoutsBox.prototype.hideLoader = function () {
    this.$el.removeClass('loading');
  };

  LayoutsBox.prototype.getLayouts = function () {
    if (this.fetchedLayouts) return;
    const self = this;
    $.ajax({
      type: 'GET',
      url: this.url,
      beforeSend() {
        self.showLoader();
      },
      success(data) {
        self.fetchedLayouts = true;
        self.$content.html(data);
        self.$blockCacheModal = $('#clearBlockCachesModal');
        self.initLayouts();
        self.hideLoader();
      },
    });
  };

  const submitModal = (url, modal, method, csrf, body, afterSuccess, afterError) => {
    fetch(url, {
      method,
      credentials: 'same-origin',
      headers: {
        'X-CSRF-Token': csrf,
      },
      body,
    }).then((response) => {
      if (!response.ok) {
        return response.text().then((data) => {
          modal.insertModalHtml(data);
          if (afterError) afterError();
          throw new Error(`HTTP error, status ${response.status}`);
        });
      }
      return response.text();
    }).then((data) => {
      // eslint-disable-next-line dot-notation
      const layoutUrl = JSON.parse(data)['layout_url'];
      // eslint-disable-next-line dot-notation
      const returnUrl = JSON.parse(data)['return_url'];
      window.localStorage.setItem('ngl_referrer', returnUrl);
      window.location.replace(layoutUrl);
      modal.close();
      // if (afterSuccess) afterSuccess(data);
      return true;
    }).catch((error) => {
      console.log(error); // eslint-disable-line no-console
    });
  };

  /* save current location ID to local storage when opening Netgen Layouts edit interface */
  $(document).on('click', '.js-open-ngl', function (e) {
    localStorage.setItem('ngl_referrer', window.location.href);
    e.currentTarget.dataset.valueId !== undefined && localStorage.setItem('ngl_referrer_value_id', e.currentTarget.dataset.valueId);
    e.currentTarget.dataset.valueType !== undefined && localStorage.setItem('ngl_referrer_value_type', e.currentTarget.dataset.valueType);
  });

  $(document).on('click', '.js-direct-mapping', function () {
    const layoutId = document.querySelector('.mapped-layouts-box').dataset.url.split('/').pop();
    const apiUrl = `${window.location.origin}/${window.location.pathname.split('/')[1]}`;
    const baseUrl = `${apiUrl}/ngadmin/layouts`;
    const url = `${baseUrl}/${layoutId}/wizard`;
    const modal = new NlModal({
      preload: true,
      autoClose: false,
    });
    const formAction = (ev) => {
      ev.preventDefault();
      modal.loadingStart();
      const formEl = modal.el.getElementsByTagName('FORM')[0];
      const afterSuccess = () => {};
      submitModal(url, modal, 'POST', this.csrf, new URLSearchParams(new FormData(formEl)), afterSuccess);
    };
    fetch(url, {
      method: 'GET',
    }).then((response) => {
      if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
      return response.text();
    }).then((data) => {
      modal.insertModalHtml(data);
      modal.disableForm();
      modal.el.addEventListener('apply', formAction);
    }).catch((error) => {
      console.log(error); // eslint-disable-line no-console
    });
  });

  $(document).on('change', '.rules-checkbox', function () {
    if (this.checked) {
      $('.rule-non-direct').show();

      $('.title-direct-rules').hide();
      $('.title-all-rules').css('display', 'flex');
    } else {
      $('.rule-non-direct').hide();

      $('.title-direct-rules').css('display', 'flex');
      $('.title-all-rules').hide();
    }
  });

  $('.mapped-layouts-box').each(function () {
    return new LayoutsBox(this);
  });
});
