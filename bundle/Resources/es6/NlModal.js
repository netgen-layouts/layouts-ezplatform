import NlMoveModalGroup from './NlMoveModalGroup';

/* modal plugin */
export default class NlModal {
    constructor(opt, parentModal = null) {
      // eslint-disable-next-line prefer-object-spread
      this.options = Object.assign({
        preload: false,
        cancelDisabled: false,
        autoClose: true,
        body: '<p>Empty modal</p>',
        title: '',
        cancelText: 'Cancel',
        applyText: 'OK',
      }, opt);
      [this.appEl] = document.getElementsByClassName('panel-primary');
      this.el = document.createElement('div');
      this.el.className = 'nl-modal-mask';
      if (this.options.className) this.el.classList.add(this.options.className);
      this.container = document.createElement('div');
      this.container.className = 'nl-modal-container';
      this.loader = document.createElement('div');
      this.loader.className = 'nl-modal-loader';
      this.loader.innerHTML = '<span></span>';

      // move modal extras
      this.modalGroups = {};
      this.parentModal = parentModal;
      this.selectedGroup = null;
      [this.chosenGroup] = document.getElementsByClassName('chosen-group');
      this.hiddenInput = document.getElementById('layout_wizard_rule_group');

      this.onKeyDown = (e) => {
        e.keyCode === 27 && this.close();
        e.keyCode === 13 && e.preventDefault();
      };

      this.onKeyDown = this.onKeyDown.bind(this);

      this.parentModal === null ? this.loadModal() : this.loadGroupModal();
      this.setupEvents();
    }

    deleteValidation() {
      const regex = new RegExp(this.deleteInput.pattern);
      if (regex.test(this.deleteInputValue)) {
        this.applyElement.disabled = false;
      } else {
        this.applyElement.disabled = true;
      }
    }

    loadModal() {
      this.options.preload ? this.loadingStart() : this.container.innerHTML = this.getHtml();
      this.el.appendChild(this.loader);
      this.el.appendChild(this.container);
      this.appEl.appendChild(this.el);
      window.addEventListener('keydown', this.onKeyDown);
    }

    checkForm() {
        this.el.querySelector('#layout_wizard_action_0').checked ? this.disableForm() : this.enableForm();
      }

    handleCheckbox(id) {
      Object.keys(this.modalGroups).forEach((key) => this.modalGroups[key].handleCheckbox(id));
      // id === null ? this.moveButton.disabled = true : this.moveButton.disabled = false;
    }

    loadContent() {
      const basePath = document.querySelector('[name="ezadmin-base-path"]').getAttribute('content').replace(/\/$/, '');
      const apiUrl = `${window.location.origin}${basePath}`;
      const url = `${apiUrl}/nglayouts/admin/api/mappings/groups/root`;
      [this.modalBody] = this.container.getElementsByClassName('nl-modal-body');
      // this.addModalGroup({ id: '00000000-0000-0000-0000-000000000000', name: 'root' });

      fetch(url, {
        method: 'GET',
      }).then((response) => {
        if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
        return response.text();
      }).then((data) => {
        const parsedData = JSON.parse(data);
        this.addModalGroup(parsedData.group);
      }).then(() => {
        this.modalBody.appendChild(this.modalGroups[Object.keys(this.modalGroups)[0]].el);
      })
        .catch((error) => {
          console.log(error); // eslint-disable-line no-console
        });
    }

    loadGroupModal() {
      this.container.innerHTML = this.getHtml();
      this.loadContent();
      this.el.appendChild(this.loader);
      this.el.appendChild(this.container);
      this.appEl.appendChild(this.el);
      window.addEventListener('keydown', this.onKeyDown);
      [this.moveButton] = this.container.getElementsByClassName('action-apply');
      [this.actions] = this.container.getElementsByClassName('nl-modal-actions');
      [this.modalWrapperEl] = this.container.getElementsByClassName('nl-modal');
      this.modalWrapperEl.style.display = 'flex';
      // this.group.moveId === null ? this.moveButton.disabled = true : null;
    }

    // eslint-disable-next-line class-methods-use-this
    getGroupHtml(id, name) {
      return `
      <div class="nl-group-content">
          <div class="nl-group-head">
              <div class="nl-rule-cell">
                  <div class="nl-export-checkbox">
                      <input type="checkbox" id="select${id}" classname="move-modal-group-select">
                      <label for="select${id}"></label>
                  </div>
              </div>
              <div class="nl-rule-cell group-title js-group-title">
                  <span class="icon-group-open">
                  </span>
                  <span class="icon-group">
                  </span>
                  <span class="icon-group-disabled-open">
                      <div class="disabled-tooltip">Group disabled</div>
                  </span>
                  <span class="icon-group-disabled">
                      <div class="disabled-tooltip">Group disabled</div>
                  </span>
                  <p>${name}</p>
              </div>
          </div>

          <div class="nl-group-body">
              <div class="nl-grid">
                  <div class="nl-group-list col-xs12"></div>
              </div>
          </div>
      </div>`;
    }

    addModalGroup(data) {
      const newModalGroupEl = document.createElement('div');
      newModalGroupEl.className = 'nl-group nl-element';
      newModalGroupEl.innerHTML = this.getGroupHtml(data.id, data.name);
      const newModalGroup = new NlMoveModalGroup(newModalGroupEl, data, this);
      this.modalGroups[data.id] = newModalGroup;
    }

    getHtml() {
      return `<div class="nl-modal">
                        <button class="close-modal"></button>
                        <div class="nl-modal-head">${this.options.title}</div>
                        <div class="nl-modal-body">${this.parentModal === null ? this.options.body : ''}</div>
                        <div class="nl-modal-actions">
                            <button type="button" class="nl-btn nl-btn-default action-cancel">${this.options.cancelText}</button>
                            <button type="button" class="nl-btn nl-btn-primary action-apply">${this.options.applyText}</button>
                        </div>
                    </div>`;
    }

    disableForm() {
        this.el.querySelector('#layout_wizard_layout').style.display = 'none';
        this.el.querySelector('#layout_wizard_layout').previousElementSibling.style.display = 'none';
        this.el.querySelector('#layout_wizard_layout_type').style.display = 'flex';
        this.el.querySelector('#layout_wizard_layout_type').previousElementSibling.style.display = 'block';
      }

      enableForm() {
        this.el.querySelector('#layout_wizard_layout').style.display = 'block';
        this.el.querySelector('#layout_wizard_layout').previousElementSibling.style.display = 'block';
        this.el.querySelector('#layout_wizard_layout_type').style.display = 'none';
        this.el.querySelector('#layout_wizard_layout_type').previousElementSibling.style.display = 'none';
      }

    setupEvents() {
      this.el.addEventListener('click', (e) => {
        if (e.target.closest('.close-modal')) {
          this.close(e);
        } else if (e.target.closest('.action-apply')) {
          this.parentModal === null ? this.apply(e) : this.setChosenGroup();
        } else if (e.target.closest('.action-cancel')) {
          this.cancel(e);
        } else if (e.target.closest('.choose-group')) {
          e.preventDefault();
          window.removeEventListener('keydown', this.onKeyDown);
          // eslint-disable-next-line no-unused-vars
          const modal = new NlModal({
            preload: true,
            autoClose: false,
          }, this);
        } else if (e.target.closest('#layout_wizard_action_0') || e.target.closest('#layout_wizard_action_1')) {
          e.target.value === 'new_layout' ? this.disableForm() : this.enableForm();
        }
      });
    }

    setChosenGroup(e) {
      e && e.preventDefault();
      this.chosenGroup.innerHTML = this.selectedGroup.name;
      this.hiddenInput.value = this.selectedGroup.id;
      this.close();
    }

    apply(e) {
      e && e.preventDefault();
      this.el.dispatchEvent(new Event('apply'));
      this.options.autoClose && this.close();
    }

    cancel(e) {
      e && e.preventDefault();
      this.el.dispatchEvent(new Event('cancel'));
      this.close();
    }

    close(e) {
      e && e.preventDefault();
      this.el.dispatchEvent(new Event('cancel'));
      this.destroy();
      window.removeEventListener('keydown', this.onKeyDown);
      this.parentModal && this.parentModal.childClosed();
    }

    childClosed() {
        window.addEventListener('keydown', this.onKeyDown);
    }

    deleteSetup() {
      this.deleteInput = document.getElementById('delete-verification');
      [this.applyElement] = this.el.getElementsByClassName('action-apply');
      this.deleteInputValue = '';
      if (this.deleteInput) {
        this.applyElement.disabled = true;
      }

      if (this.deleteInput) {
        this.deleteInput.addEventListener('keyup', (e) => {
          this.deleteInputValue = e.target.value;
          this.deleteValidation();
        });
      }
    }

    insertModalHtml(html) {
      this.container.innerHTML = html;
      this.loadingStop();
      this.deleteSetup();
    }

    loadingStart() {
      this.el.classList.add('modal-loading');
    }

    loadingStop() {
      this.el.classList.remove('modal-loading');
    }

    destroy() {
      this.el.dispatchEvent(new Event('close'));
      this.el.parentElement && this.el.parentElement.removeChild(this.el);
    }
  }
