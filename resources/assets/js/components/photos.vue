<template>
    <div class="entity-photos">
        <div v-if="type === 'full'" class="photos-full">
            <div class="photosList row">
                <div v-if="model.photos" class="col-md-12">
                    <div v-for="(size,index) in model.photos" class="photo_cont">
                        <div><img :src="size.small"/></div>
                    </div>
                </div>
            </div>
            <div class="small-info">Можно вставлять картинки из буфера</div>
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
                    <button type="button" class="btn btn-secondary">
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
    module.exports = Vue.extend({
        props: {
            'entity': String,
            'id': Number,
            'type': {
                'default': 'full'
            }
        },
        data: function () {
            return {
                model: {}
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;
                Data.entity.get(this.entity, this.id).then(function (res) {
                    self.$set(self,'model', res);
                });
            },
            loadFiles(e) {
                let data = new FormData();
                data.append('id', this.id);
                data.append('entity', this.entity);
                for (var i = 0; i < e.target.files.length; i++) {
                    let file = e.target.files.item(i);
                    data.append('images[' + i + ']', file, file.name);
                }
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                };

                let self = this;
                axios.post('/shop?method=uploadImgs', data, config).then(function (res) {
                    self.loadData();
                });
            }
        }
    });
</script>
<style>
    .entity-photos .photosList {
        height: 200px;
    }

    .entity-photos h4 {
        margin: 5px 0;
        color: #999;
    }

    .entity-photos .small-info {
        text-align: center;
        font-size: 12px;
        color: #999;
    }

    .photo_cont{
        border: 2px solid #777;
        padding:15px;
        float:left;
        margin: 0 15px 0 0;
    }
</style>