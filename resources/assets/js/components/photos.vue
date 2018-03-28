<template>
    <div @paste="pasteImage" class="entity-photos">
        <div v-if="type === 'full'" class="photos-full">
            <draggable v-model="photos" class="photosList row" @end="reOrder">
                <transition-group v-if="photos" class="col-md-12">
                    <div v-for="(photo,index) in photos" :key="index" class="photo_cont">
                        <span class="photo-num">{{photo.photo_id}}</span>
                        <span @click.prevent="deleteImg(photo.photo_id)" class="glyphicon glyphicon-remove"></span>
                        <div class="image" @click="viewOriginalImg(photo)"><img
                                :src="photo.urls.small+'?'+Math.random()"/></div>
                        <div class="text-muted text-center">{{photo.width}}x{{photo.height}}</div>
                        <hr/>
                        <div>
                            <button type="button" @click.prevent="rotateImg(photo.photo_id,-90)"
                                    class="btn btn-secondary btn-sm">
                                <span class="mirrorX glyphicon glyphicon-repeat"></span>
                            </button>
                            <button type="button" @click.prevent="rotateImg(photo.photo_id,90)"
                                    class="btn btn-secondary btn-sm float-right">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </button>
                        </div>
                        <hr/>
                        <div>
                            <button @click.prevent="toggleHideImg(photo.photo_id,!photo.hidden)" type="button"
                                    class="w-100 btn btn-secondary btn-sm">
                                <span class="glyphicon"
                                      :class="(photo.hidden)?'glyphicon-check':'glyphicon-unchecked'"></span>
                                Скрыть фото
                            </button>
                        </div>
                    </div>
                </transition-group>
            </draggable>
            <div class="small-info mt-2">Можно вставлять картинки из буфера</div>
            <hr/>
            <div>
                <div class="float-left"><h4>Закачать с жесткого диска:</h4></div>
                <div class="float-left ml-4 mr-5">
                    <input class="d-none" type="file" name="images[]" multiple="true" @change="loadFiles">
                    <button type="button" class="btn btn-secondary"
                            @click="$event.target.previousElementSibling.click()">
                        <span class="glyphicon glyphicon-cloud-upload"></span>
                        Выбрать файл
                    </button>
                </div>
                <div>
                    <button @click="reloadImages()" type="button" class="btn btn-secondary">
                        <span class="glyphicon glyphicon-refresh"></span>
                        Обновить картинки
                    </button>
                </div>
            </div>
            <hr/>
        </div>
        <div v-else>

        </div>
    </div>
</template>
<script>
    let contentModal = require('./contentModal.vue');
    module.exports = Vue.extend({
        components: {
            'draggable': require('vuedraggable'),
        },
        props: {
            'entity': String,
            'id': Number,
            'type': {
                'default': 'full'
            }
        },
        data: function () {
            return {
                controller: '/common/images',
                photos: []
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;
                return Ajax.post(this.controller, 'getEntityPhotos', {'entity': this.entity, 'id': this.id})
                    .then(function (res) {
                        self.$set(self, 'photos', res['photos']);
                        Vue.Events.emit('entity_edit:setAttribute', [self.entity, self.id, {'photos': res['short_list']}]);
                    });
            },
            loadFiles(e) {
                let data = new FormData();
                data.append('id', this.id);
                data.append('entity', this.entity);
                for (let i = 0; i < e.target.files.length; i++) {
                    let file = e.target.files.item(i);
                    data.append('images[' + i + ']', file, file.name);
                }
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                };

                let self = this;
                return axios.post(this.controller+'?method=uploadImgs', data, config).then(function (res) {
                    self.loadData();
                });
            },
            reOrder() {
                let self = this;
                let new_order = [];
                for (let i in this.photos) {
                    new_order.push(this.photos[i].photo_id);
                }
                return Ajax.post(this.controller, 'reOrder', {
                    'entity': this.entity,
                    'id': this.id,
                    'new_order': new_order
                }).then(function (res) {
                    self.loadData();
                });
            },
            rotateImg(num, side) {
                let self = this;
                return Ajax.post(this.controller, 'rotateImg', {
                    'entity': this.entity,
                    'id': this.id,
                    'num': num,
                    'side': side
                }).then(function (res) {
                    self.loadData();
                });
            },
            toggleHideImg(num, hide) {
                let self = this;
                return Ajax.post(this.controller, 'toggleHideImg', {
                    'entity': this.entity,
                    'id': this.id,
                    'num': num,
                    'hide': hide
                }).then(function (res) {
                    console.log(res);
                    self.loadData();
                });
            },
            deleteImg(num) {
                let self = this;

                return Ajax.post(this.controller, 'deleteImg', {
                    'entity': this.entity,
                    'id': this.id,
                    'num': num
                }).then(function (res) {
                    self.loadData();
                });
            },
            viewOriginalImg(photo) {
                let $modal = new contentModal({
                    propsData: {
                        'body': '<div class="text-center"><img style="max-width:100%;" src="' + photo.urls.big + '?' + Math.random() + '"></div>'
                    }
                });
            },
            pasteImage(event) {
                let items = (event.clipboardData || event.originalEvent.clipboardData).items;
                for (let index in items) {
                    let item = items[index];
                    if (item.kind === 'file') {
                        let blob = item.getAsFile();
                        let reader = new FileReader();
                        reader.onload = this.filePasted;
                        reader.readAsDataURL(blob);
                    }
                }
            },
            filePasted(event){
                AppNotifications.add({
                    'type': 'warning',
                    'body': 'Ожидайте, файл загружается...'
                });
                let self = this;
                let data = new FormData();
                data.append('id', self.id);
                data.append('entity', self.entity);

                data.append('image', event.target.result);

                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                };

                return axios.post(this.controller+'?method=uploadImgs', data, config).then(function (res) {
                    self.loadData();
                });
            },
            reloadImages(){
                let self = this;
                return Ajax.post(this.controller, 'reloadImages', {
                    'entity': this.entity,
                    'id': this.id,
                }).then(function (res) {
                    self.loadData();
                });
            }
        }
    });
</script>
<style>
    .entity-photos {
        position: relative;
    }

    .entity-photos .glyphicon-remove {
        cursor: pointer;
    }

    .entity-photos .photosList {
        min-height: 200px;
    }

    .entity-photos h4 {
        margin: 5px 0;
        color: #999;
    }

    .entity-photos .image {
        height: 100px;
        cursor: zoom-in;
    }

    .entity-photos img {
        max-height: 100px;
    }

    .entity-photos .small-info {
        text-align: center;
        font-size: 12px;
        color: #999;
    }

    .entity-photos .photo-num {
        position: absolute;
        font-weight: bold;
        top: 5px;
        left: 5px;
        line-height: 1;
        font-size: 18px;
    }

    .entity-photos .glyphicon-remove {
        position: absolute;
        color: #d20000;
        top: 5px;
        right: 5px;
    }

    .entity-photos hr {
        margin: 10px -20px;
    }

    .entity-photos .photo_cont {
        position: relative;
        border: 2px solid #777;
        padding: 20px;
        float: left;
        margin: 0 15px 0 0;
    }

    .mirrorX {
        transform: scale(-1, 1);
    }
</style>