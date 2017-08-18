import SectionList from './components/SectionList.vue';
import SectionView from './components/SectionView.vue';
import EntityEdit from './components/EntityEdit.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        curSection: null,
        filters: {},
        fields: {},
    },
    beforeCreate: function () {
    },
    mounted: function () {
        this.setMainParams();
        this.buildApp();
    },
    methods: {
        setMainParams() {
            this.curSection = Url.get('section');
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
            if (!this.curSection) {
                this.showEmpty();
            } else {
                this.showSection(this.curSection);
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

