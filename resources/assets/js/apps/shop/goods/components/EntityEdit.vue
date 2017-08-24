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
                <div v-show="index == curTab" :ref="index">
                    <div v-if="tab.content" class="mt-2">
                        <div v-for="row in tab.content">
                            <div class="row">
                                <div v-for="field in row" :class="((field.size)?'col-md-'+field.size:'')">
                                    <div v-if="Fields[field.field]">
                                        <div class="label">{{Fields[field.field].title}}</div>
                                        <div v-if="Fields[field.field].type==='int'">
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
                                                    @click="Model[field.field] = !Model[field.field]    "
                                                    :disabled="isNoEditable(field.field)">
                                                <span class="glyphicon"
                                                      :class="(Model[field.field] )?'glyphicon-check':'glyphicon-unchecked'"></span>
                                            </button>
                                        </div>
                                    </div>
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
    let ConfirmModal = require('../../../../components/confirmModal.vue');
    module.exports = Vue.extend({
        data: function () {
            return {
                'id': null,
                'entity': null,
                'curTab': 'tab_1',
                'Fields': {},
                'Model': {},
                'BaseModel': {},
                'packages': {
                    'Sections': require('../packages/section.js')
                }
            };
        },
        beforeCreate() {
            let self = this;
            let Info = this.$options.data();
            if (Info.entity) {
                Data.entity.getAllFields(Info.entity).then(function (res) {
                    self.$set(self, 'Fields', res);
                });

                if (Info.id) {
                    Data.entity.get(Info.entity, Info.id, true).then(function (res) {
                        self.$set(self, 'Model', self.cloneObject(res));
                        self.$set(self, 'BaseModel', self.cloneObject(res));
                    });
                }
            }
        },
        methods: {
            getPackage() {
                if (this.entity && this.packages[this.entity]) {
                    return this.packages[this.entity];
                } else return {};
            },
            showPhotoEditor() {

            },
            isNoEditable(field) {
                if (typeof this.Fields[field].editable !== 'undefined' && !Boolean(this.Fields[field].editable)) {
                    return true;
                } else return false;
            },
            generateUrlFromTitle() {
                let self = this;
                Ajax.post('/shop', 'generateUrl', {'entity': this.entity, 'id': this.id})
                    .then(function (res) {
                        if (res.result && res.url) {
                            self.$set(self.Model, 'url', res.url);
                        }
                        if(res.errors){
                            for(let err in res.errors){
                                AppNotifications.add({
                                    'type':'danger',
                                    'body': res.errors[err],
                                });
                            }
                        }
                    });
            },
            saveEntity() {
                let newData = {};
                for(let i in this.Model){
                    if(typeof this.BaseModel[i] === 'undefined' || this.BaseModel[i]!==this.Model[i]){
                        newData[i] = this.Model[i];
                    }
                }
                Data.entity.update(this.entity, this.id, newData).then(function(res){
                    if(res.result){
                        AppNotifications.add({
                            'type':'success',
                            'body': 'Сохранено успешно!'
                        });
                    }
                    if(res.errors){
                        for(let err in res.errors){
                            AppNotifications.add({
                                'type':'danger',
                                'body': res.errors[err],
                            });
                        }
                    }
                });
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
            }
        }
    });
</script>
<style>
    h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

    button {
        cursor: pointer;
    }
</style>