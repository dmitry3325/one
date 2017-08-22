<template>
    <div>

        <input class="form-control" @keyup="search($event.target.value)" placeholder="Поиск" />
        <br/>
        <div class="list">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered">
                    <thead>
                    <tr>
                        <th v-for="field in fields">{{field.title}}</th>
                        <th>Hello</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td v-for="(field, key) in fields"><input @change="updateField($event, item['id'], key)"
                                                                  v-bind:value="item[key]" :disabled="field.disabled">
                        </td>
                        <td>
                            <img v-if="item['goods_photo'].length > 0" v-for="img in item['goods_photo']" :src="img.path" width="100px" />
                            <input v-if="item['goods_photo'].length === 0" type="file" @change="onFileChange($event, item['id'])">

                        </td>
                    </tr>
                    </tbody>

                </table>
                <div v-else class="text-center"><h3>Ничего не нашлось</h3></div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'loading': true,
                'items': {},
                'fields': {},
            }
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;

                let q = [
                    Data.vendors.getAllFields(),

                    Data.vendors.getVendorsList({
                        filters: this.filters,
                        fields: this.fields
                    }),
                ];


                Ajax.when(q, function (fields, items) {
                    self.loading = false;
                    self.fields = fields;

                    if (items.result && items.list) {
                        self.$set(self, 'items', items.list);
                    }

                });

            },
            updateField(event, id, field) {
                let options = {};
                options[field] = event.target.value;

                Data.vendors.update(id, options);
            },
            search(value) {
                let self = this;
                Data.vendors.getVendorsList().then(function(all){

                    let result = [];

                    for (let i = 0; i < all.length; i++) {
                        if (all[i]['id'] == value || all[i]['title'].indexOf(value) != -1) {
                            result.push(all[i]);
                        }
                    }

                    self.$set(self, 'items', result);
                });
            },
            onFileChange(e, id) {
                let files = e.target.files || e.dataTransfer.files;
                console.log(files);

                if (!files.length){
                    return;
                }
                this.createImage(files[0]);
            },
            createImage(file) {
                let image = new Image();
                let reader = new FileReader();

                reader.onload = (e) => {
                    options['goods_photo']['path'] = e.target.result;
                    Data.vendors.update(id, options);

                };
                reader.readAsDataURL(file);
            },
        }
    });
</script>