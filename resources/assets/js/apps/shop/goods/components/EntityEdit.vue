<template>
    <div class="Editor">
        <div><h1>{{((Model.title) ? Model.title : (Model.id) ? 'ID: ' + Model.id : '-')}}</h1></div>
        <ul class="nav nav-tabs">
            <li v-for="(tab,index) in getPackage()" class="nav-item">
                <a class="nav-link" :class="{active: index == curTab}" href=""
                   @click.prevent="curTab = index; ((tab.function)?call(tab.function):'')">
                    {{tab.title}}
                </a>
            </li>
            <div class="ml-auto">
                <button class="btn btn-success" @click="saveEntity"><span
                        class="glyphicon glyphicon-floppy-save"></span> Сохранить
                </button>
            </div>
            <div class="ml-3">
                <button class="btn btn-danger" @click="deleteEntity"><span class="glyphicon glyphicon-remove"></span>
                    Удалить
                </button>
            </div>
        </ul>
        <div class="content">
            <div v-for="(tab,index) in getPackage()">
                <div v-show="index == curTab" class="mt-3" :ref="index">
                    <div v-if="tab.content">
                        <div v-for="row in tab.content" class="mb-2 row">
                            <div v-for="field in row"
                                 :class="((field.size)?'col-md-'+field.size:'') + ' ' + ((field.class)?field.class:'')">
                                <div v-if="field.type && field.type =='info' && field.model">
                                    <span v-if="_self[field.model] && _self[field.model][field.field]"
                                        class="badge badge-default  ">
                                        {{_self[field.model][field.field]}}
                                    </span>
                                </div>
                                <div v-else-if="Fields[field.field]">
                                    <div class="label">{{Fields[field.field].title}}</div>
                                    <div v-if="Fields[field.field].type==='int' || Fields[field.field].type==='double'">
                                        <input type="number" class="form-control" v-model="Model[field.field]"
                                               :disabled="isNoEditable(field.field)"/>
                                    </div>
                                    <div v-if="Fields[field.field].type==='input'" class="input-group">
                                        <input type="text" class="form-control" v-model="Model[field.field]"
                                               :disabled="isNoEditable(field.field)"/>
                                        <span v-if="field.add && field.add.el == 'button'" class="input-group-btn">
                                                <button class="btn btn-secondary" type="button"
                                                        @click="((field.add.function)?call(field.add.function):false)">
                                                    {{field.add.title}}
                                                </button>
                                            </span>
                                    </div>
                                    <div v-if="Fields[field.field].type==='textarea'">
                                            <textarea class="form-control" rows="5" v-model="Model[field.field]"
                                                      :disabled="isNoEditable(field.field)"></textarea>
                                    </div>
                                    <div v-if="Fields[field.field].type==='checkbox'">
                                        <button type="button" class="btn"
                                                :class="(Model[field.field])?'btn-success':'btn-secondary'"
                                                @click="$set(Model,field.field, !Model[field.field])"
                                                :disabled="isNoEditable(field.field)">
                                                <span class="glyphicon"
                                                      :class="(Model[field.field])?'glyphicon-check':'glyphicon-unchecked'"></span>
                                        </button>
                                    </div>
                                    <div v-if="Fields[field.field].type==='select'">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-toggle="dropdown">
                                                {{(Model[field.field] && Fields[field.field]['options'][Model[field.field]]) ?
                                                Fields[field.field]['options'][Model[field.field]] :
                                                ((Fields[field.field].empty_text) ? Fields[field.field].empty_text : 'Выберите')}}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a v-for="(option,index) in Fields[field.field]['options']"
                                                   @click="$set(Model,field.field, index)"
                                                   class="dropdown-item" href="#">
                                                    {{option}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="field.content" v-html="field.content"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="tab.component">
                        <div v-if="tab.component == 'photos'">
                            <photos :entity="entity" :id="id"></photos>
                        </div>
                        <div v-else-if="tab.component == 'filters'">
                            <filters :entity="entity" :id="id"></filters>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    let Photos = require('../../../../components/photos.vue');
    let Filters = require('./Filters.vue');
    let ConfirmModal = require('../../../../components/confirmModal.vue');
    module.exports = Vue.extend({
        components: {
            'photos': Photos,
            'filters': Filters
        },
        props: {
            'id': Number,
            'entity': String,
        },
        data: function () {
            return {
                'curTab': 'common',
                'Fields': {},
                'Model': {},
                'Section': {},
                'BaseModel': {},
                'packages': {
                    'Sections': require('../packages/section.js'),
                    'Filters': require('../packages/filter.js'),
                    'Goods': require('../packages/good.js')
                }
            };
        },
        mounted() {
            let self = this;
            if (this.entity) {
                Data.entity.getAllFields(this.entity).then(function (res) {
                    self.$set(self, 'Fields', res);
                });

                if (this.id) {
                    Data.entity.get(this.entity, this.id, true).then(function (res) {
                        self.$set(self, 'Model', Funs.cloneObject(res));
                        self.$set(self, 'BaseModel', Funs.cloneObject(res));

                        if (self.Model.section_id) {
                            Data.sections.get(self.Model.section_id).then(function (res) {
                                self.$set(self, 'Section', Funs.cloneObject(res));
                            });
                        }
                    });

                }
            }
            Vue.Events.on('entity_edit:setAttribute', this.setAttribute);
        },
        methods: {
            getPackage() {
                if (this.entity && this.packages[this.entity]) {
                    return this.packages[this.entity];
                } else return {};
            },
            isNoEditable(field) {
                if (typeof this.Fields[field].editable !== 'undefined' && !Boolean(this.Fields[field].editable)) {
                    return true;
                } else return false;
            },
            generateUrlFromTitle() {
                let self = this;
                Ajax.post('/shop', 'generateUrl', {'entity': this.entity, 'id': this.id, 'title': this.Model.title})
                    .then(function (res) {
                        if (res.result && res.url) {
                            self.$set(self.Model, 'url', res.url);
                        }
                        if (res.errors) {
                            for (let err in res.errors) {
                                AppNotifications.add({
                                    'type': 'danger',
                                    'body': res.errors[err],
                                });
                            }
                        }
                    });
            },
            saveEntity(e) {
                let newData = {};
                for (let i in this.Model) {
                    if (typeof this.BaseModel[i] === 'undefined' || this.BaseModel[i] !== this.Model[i]) {
                        newData[i] = this.Model[i];
                    }
                }

                let $el = e.target;
                $el.setAttribute("disabled", true);
                Data.entity.update(this.entity, this.id, newData).then(function (res) {
                    if (res.result) {
                        AppNotifications.add({
                            'type': 'success',
                            'body': 'Сохранено успешно!'
                        });
                    }
                    if (res.errors) {
                        for (let err in res.errors) {
                            AppNotifications.add({
                                'type': 'danger',
                                'body': res.errors[err],
                            });
                        }
                    }
                    $el.removeAttribute("disabled");
                });
                Vue.Events.emit('entity_save');
            },
            deleteEntity() {
                let self = this;
                new ConfirmModal({
                    'data': {
                        'body': 'Уверены, что хотите удалить страницу безвозвратно?',
                        'confirm_func': function () {
                            Data.entity.deleteEntity(self.entity, self.id)
                                .then(function (res) {
                                    if (res.result) {
                                        self.$set(self, 'Model', {});
                                        self.$set(self, 'BaseModel', {});
                                    }
                                });
                        }
                    }
                });
            },
            setAttribute(enity, id, data){
                if(this.entity !== enity || this.id !== id) return;
                for(var i in data){
                    this.$set(this.Model,i, data[i]);
                }
            }
        }
    });
</script>
<style>
    .Editor h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

    .Editor button {
        cursor: pointer;
    }

    .Editor .row {
        margin-right: -10px;
        margin-left: -10px;
    }
    .Editor .section-filter-title .badge{
        width: 100%;
        margin-top:25px;
        font-size: 24px;
    }
</style>