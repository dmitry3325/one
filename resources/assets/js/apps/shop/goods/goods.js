import SectionList from './components/SectionList.vue';
import SectionView from './components/SectionView.vue';
import EntityEdit from './components/EntityEdit.vue';
import Photos from '../../../components/photos.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        curSection: null,
        curEntity: null,
        curEntityId: null,
        filters: {},
        fields: {},
    },
    mounted: function () {
        this.setMainParams();
        this.buildApp();
    },
    methods: {
        setMainParams() {
            this.curSection = Url.get('section');
            this.curEntity = Url.get('entity');
            this.curEntityId = Url.get('id');
        },
        buildApp() {
            let self = this;
            let $sectionList = this.setTarget('.section_list');
            new SectionList({
                el: $sectionList,
                data: {
                    curSection: this.curSection,
                    selectAction: function (id) {
                        Url.unset('filters');
                        self.showSection(id);
                    },
                    toggleAction: this.toggleMenu

                }
            });
            if (this.curEntity && this.curEntityId) {
                this.showEntityEdit(this.curEntity, this.curEntityId);
            } else if (this.curSection) {
                this.showSection(this.curSection);
            } else {
                this.showEmpty();
            }
        },
        toggleMenu(show) {
            if (typeof show === 'undefined') {
                document.querySelector('.section_list').classList.toggle('d-none');
            } else if (show) {
                document.querySelector('.section_list').classList.remove('d-none');
            } else {
                document.querySelector('.section_list').classList.add('d-none');
            }
        },
        showEmpty() {

        },
        showEntityEdit(entity, id) {
            if (!this.$content) {
                this.$content = document.querySelector('.main-content');
            }

            this.$content.innerHTML = '';
            let $el = this.setTarget(this.$content);

            new Photos({
                el: $el,
                propsData: {
                    id: parseInt(id),
                    entity: entity,
                },
            });
            this.toggleMenu(false);
            return;
            new EntityEdit({
                el: $el,
                propsData: {
                    id: parseInt(id),
                    entity: entity,
                },
            });

            this.toggleMenu(false);
        },
        showSection(id) {
            this.curSection = id;
            Url.set('section', id);
            if (!this.$content) {
                this.$content = document.querySelector('.main-content');
            }

            this.$content.innerHTML = '';
            let $el = this.setTarget(this.$content);

            new SectionView({
                el: $el,
                data: {
                    id: this.curSection,
                    filters: Url.get('filters'),
                    showMenu: this.toggleMenu
                }
            });

            this.toggleMenu(false);
        }
    }
});

