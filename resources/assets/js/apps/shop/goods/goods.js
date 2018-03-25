import SectionList from './components/SectionList.vue';
import SectionView from './components/SectionView.vue';
import EntityEdit from './components/EntityEdit.vue';
import Search from './components/Search.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        curSection: null,
        curEntity: null,
        curEntityId: null,
        search: null,
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
            this.initSectionsList();
            this.initSearch();

            if (this.curEntity && this.curEntityId) {
                this.showEntityEdit(this.curEntity, this.curEntityId);
            } else if (this.curSection) {
                this.showSection(this.curSection);
            } else {
                this.showEmpty();
            }
        },
        initSectionsList(){
            if(this.sectionsList) return;
            let self = this;
            let $sectionList = this.setTarget('.section_list');
            this.sectionsList = new SectionList({
                el: $sectionList,
                data: {
                    curSection: this.curSection,
                    selectAction: function (id) {
                        Url.unset('filters');
                        Url.unset('search');
                        Url.unset('tab');
                        self.showSection(id);
                    },
                    toggleAction: this.toggleMenu

                }
            });
        },
        initSearch(){
            if(this.Search) return;
            let $search = this.setTarget('#search');
            this.Search = new Search({
                'el': $search
            });
        },
        makeSearch(e){
            let val = e.target.value;
            if(this.Search){
                this.Search.makeSearch(val);
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
                    id: parseInt(this.curSection),
                    filters: Url.get('filters'),
                    showMenu: this.toggleMenu
                }
            });

            this.toggleMenu(false);
        }
    }
});

