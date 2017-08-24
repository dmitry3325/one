<template>
    <div class="sections-menu">
        <div class="search">
            <div class="mb-2    ">
                <button style="width: 90px;" @click="((typeof toggleAction === 'function')?toggleAction(false):false)"
                        class="btn btn-sm btn-secondary">
                    <span class="glyphicon glyphicon-arrow-left"></span> Скрыть
                </button>
                <button style="width: calc(100% - 95px);" @click="createSection"
                        class="btn btn-sm btn-info">Создать
                </button>
            </div>
            <div><input type="text" class="form-control" v-model="search" placeholder="Поиск по разделам"
                        @keyup="makeSearch"></div>
        </div>
        <div v-if="!inProcess" class="tree">
            <div v-if="!tree || !Object.keys(tree).length" class="text-center">Ничего не найдено</div>
            <div v-else>
                <div class="item" v-for="item in tree">
                    <div class="row m-0" :class="((item.data.id == selected)?'selected':'')">
                        <div class="float-left btn-cont">
                            <button v-if="item.children && Object.keys(item.children).length"
                                    class="btn btn-secondary btn-sm" @click="toggle">+</button>
                        </div>
                        <div @click="selectSection(item.data.id)" class="float-left title text-ellipsis cursor-pointer">
                            {{(item.data.title) ? item.data.title : item.data.id}}
                        </div>
                    </div>
                    <div class="children d-none">
                        <div class="row m-0">
                            <div v-for="ch in item.children" class="m-0 row"
                                 :class="((ch.id == selected)?'selected':'')">
                                <div class="float-left btn-cont in">&nbsp;</div>
                                <div @click="selectSection(ch.id)"
                                     class="float-left title text-ellipsis cursor-pointer">
                                    {{(ch.title) ? ch.title : ch.id}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'search': '',
                'tree': null,
                'list': [],
                'selected': null,
                inProcess: true
            };
        },
        mounted: function () {
            if(Url.get('section')){
                this.selected = Url.get('section');
            }
            let self = this;
            if (!this.list.length) {
                this.loadData();
            } else {
                self.buildTree();
                self.$set(self, 'inProcess', false);
            }
        },
        methods: {
            loadData(force) {
                let self = this;
                return Data.entity.getItemsList('Sections', {
                    'tree_view': true
                }, force).then(function (data) {
                    self.list = data.data;
                    self.buildTree();
                    self.$set(self, 'inProcess', false);
                });
            },
            buildTree() {
                let list = [];
                if (this.search) {
                    for (let i in this.list) {
                        if (
                            this.list[i].id.toString().indexOf(this.search) !== -1 ||
                            this.list[i].title.toString().toLowerCase().indexOf(this.search) !== -1
                        ) {
                            list.push(this.list[i]);
                        }
                    }
                } else {
                    list = this.list;
                }

                let data = {};

                for (let i in list) {
                    let item = list[i];
                    if (!item.parent_id) {
                        data[item.id] = {};
                        data[item.id].data = item;
                    }
                }
                for (let i in list) {
                    let item = list[i];
                    if (item.parent_id) {
                        if (data[item.parent_id] && !this.search) {
                            if (!data[item.parent_id].children) {
                                data[item.parent_id].children = {};
                            }
                            data[item.parent_id].children[item.id] = item;
                        } else {
                            data[item.id] = {};
                            data[item.id].data = item;
                        }
                    }
                }
                this.$set(this, 'tree', data);
            },
            makeSearch(e) {
                this.search = e.target.value.toLowerCase();
                this.buildTree();
            },
            toggle($event) {
                let $item = $event.target.closest('.item');
                let $child = $item.querySelector('.children');
                let $button = $item.querySelector('.btn');
                if ($button.innerHTML === '+') {
                    $button.innerHTML = '-';
                } else {
                    $button.innerHTML = '+';
                }

                if ($child) {
                    $child.classList.toggle('d-none');
                }
            },
            selectSection(id) {
                this.$set(this, 'selected', id);
                if (typeof this.selectAction === 'function') {
                    this.selectAction(id);
                }
            },
            createSection(e) {
                let self = this;
                e.target.classList.add('disabled');
                Data.sections.create({
                    'title': 'Новый раздел'
                }).then(function (res) {
                    if (res.result && res.id) {
                        self.selected = res.id;
                        self.loadData(true);
                        if (typeof self.selectAction === 'function') {
                            self.selectAction(res.id);
                        }
                    }
                    e.target.classList.remove('disabled');
                });
            }
        }
    });
</script>

<style>
    .sections-menu .tree {
        margin-top: 10px;
        overflow-y: auto;
        max-height: calc(100% - 120px);
    }

    .sections-menu .search {
        padding: 0 10px;
    }

    .sections-menu .children {
        background: #eee;
        margin: 10px 0;
    }

    .sections-menu .item > .row {
        padding: 0 10px;
    }

    .sections-menu .title {
        padding: 0 10px;
    }

    .sections-menu .btn-cont {
        width: 30px;
    }

    .sections-menu .title {
        width: calc(100% - 40px);
    }

    .sections-menu .btn-cont.in {
        margin-left: 10px;
    }

    .sections-menu .row.selected {
        color: #fb4900;
    }
</style>
