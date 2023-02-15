export default class NlMoveModalGroup {
    constructor(el, data, modal, depth = 0, disabled = false) {
      this.data = data;
      this.modal = modal;
      this.el = el;
      this.id = this.data.id;
      this.depth = depth;

      this.reorderPermission = this.data.permissions.mapping.reorder;

      [this.checkBoxContainer] = this.el.getElementsByClassName('nl-export-checkbox');
      // eslint-disable-next-line prefer-destructuring
      this.selectElement = this.checkBoxContainer.children[0];
      this.selected = this.selectElement && this.selectElement.checked;

      this.disabled = disabled;

      this.disabled = !this.data.permissions.mapping.edit;

      this.apiUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}`;

      this.modalGroups = {};

      [this.appEl] = document.getElementsByClassName('ng-layouts-app');

      if (this.id === '00000000-0000-0000-0000-000000000000') {
        this.el.classList.add('show-body');
        this.loadContent();
      }

      if (this.depth === 2 && this.modal.hasGroup) {
        this.disabled = true;
      }

      this.selectElement.disabled = this.disabled;

      this.setupEvents();
    }

    loadContent() {
      const url = `${this.apiUrl}/api/mappings/groups/${this.id}/list`;
      [this.groupBody] = this.el.getElementsByClassName('nl-group-body');
      this.groupBody.innerHTML = '';
      this.modalGroups = {};

      fetch(url, {
        method: 'GET',
      }).then((response) => {
        if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
        return response.text();
      }).then((data) => {
        const parsedData = JSON.parse(data);
        parsedData.groups.forEach((group) => {
          this.addModalGroup(group);
        });
      }).then(() => {
        Object.keys(this.modalGroups).forEach((key) => {
          this.groupBody.appendChild(this.modalGroups[key].el);
        });
      })
        .catch((error) => {
          console.log(error); // eslint-disable-line no-console
        });
    }

    addModalGroup(data) {
      const newModalGroupEl = document.createElement('div');
      const disabledClass = data.enabled ? '' : 'disabled';
      newModalGroupEl.className = `nl-group nl-element ${disabledClass}`;
      newModalGroupEl.innerHTML = this.getGroupHtml(data.id, data.name);
      const newModalGroup = new NlMoveModalGroup(newModalGroupEl, data, this.modal, this.depth + 1, this.moving);
      this.modalGroups[data.id] = newModalGroup;
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

    handleCheckbox(id) {
      if (id !== this.id) {
        this.checkBoxContainer.style.visibility = '';
        this.selectElement.checked = false;
        this.selected = false;
        this.el.classList.remove('selected');
      } else {
        this.checkBoxContainer.style.visibility = 'visible';
        this.selected = true;
        this.el.classList.add('selected');
        this.modal.selectedGroup = this.data;
      }
      Object.keys(this.modalGroups).forEach((key) => this.modalGroups[key].handleCheckbox(id));
    }

    setupEvents() {
      this.el.addEventListener('click', (e) => {
        if (e.target.closest('.js-group-title')) {
          e.stopPropagation();
          if (this.depth !== 2 && this.data.has_children) {
            this.el.classList.toggle('show-body');
            if (this.el.classList.contains('show-body')) {
              this.loadContent();
            }
          } else {
            // this.clearCheckboxes();
          }
        }
      });

      this.selectElement.addEventListener('change', () => {
        this.selectElement.checked ? this.modal.handleCheckbox(this.id) : this.modal.handleCheckbox(null);
      });
    }
  }
