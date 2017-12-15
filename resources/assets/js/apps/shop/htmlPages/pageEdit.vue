<template>
    <div>
        <h1>{{ item.h1_title }}</h1>
        <button class="btn btn-success my-1 float-right" @click="save()">Сохранить</button>
        <table>
            <tr>
                <td>url</td>
                <td>
                    <input v-model="url" placeholder="url" class="input-white">
                </td>
            </tr>
            <tr>
                <td>html title</td>
                <td>
                    <input v-model="html_title" placeholder="title" class="input-white">
                </td>
            </tr>
            <tr>
                <td>html_keywords</td>
                <td>
                    <input v-model="html_keywords" placeholder="keywords" class="input-white">
                </td>
            </tr>
            <tr>
                <td>html_description</td>
                <td>
                    <textarea v-model="html_description" placeholder="описание" class="input-white"></textarea>
                </td>
            </tr>
        </table>

        <editor v-model="content" @init="editorInit();" lang="html" theme="monokai" height="500px"></editor>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                content: "",
                url: null,
                html_title: null,
                html_keywords: null,
                html_description: null,
            }
        },
        props: ['item'],
        computed: {
            page: {
                get: function () {
                    return {
                        body: this.content,
                        url: this.url,
                        html_keywords: this.html_keywords,
                        html_description: this.html_description,
                        html_title: this.html_title,
                    }
                }
            }
        },
        components: {
            editor: require('vue2-ace-editor'),
        },
        methods: {
            editorInit: function () {
                require('brace/mode/html');
                require('brace/theme/monokai');
            },
            save: function () {

                if (!this.url) {
                    AppNotifications.add({
                        'type': 'danger',
                        'body': 'Заполните url'
                    });

                    return;
                }

                Data.htmlPages.update(this.item.id, this.page).then(_ => {
                    AppNotifications.add({
                        'type': 'success',
                        'body': 'сохранено'
                    });
                }).catch(e => {
                    AppNotifications.add({
                        'type': 'danger',
                        'body': 'Ошибка - ' + e
                    });
                });
            },
            findResponseMeta: function (data, name) {
                return data.data.find(meta => meta.key === name) ? data.data.find(meta => meta.key === name).value : null
            }
        },
        mounted: function () {
            let self = this;
            self.content = 'Загружаю...';
            self.url = self.item.url.url;

            Data.htmlPages.getHtmlMeta(self.item.id).then(
                data => {
                    self.content = '';
                    if (data.result && data.data.length > 0) {
                        self.content = self.findResponseMeta(data, 'body');
                        self.html_description = self.findResponseMeta(data, 'html_description');
                        self.html_title = self.findResponseMeta(data, 'html_title');
                        self.html_keywords = self.findResponseMeta(data, 'html_keywords');
                    }
                }).catch(e => {
                AppNotifications.add({
                    'type': 'danger',
                    'body': 'Ошибка при загрузке данных - ' + e
                });
            });

        }
    }
</script>

<style lang="scss">
    .input-white {
        min-width: 400px;
        padding: 0.3em 0.8em;
        display: block;
        background: #ccc;
        border: 1px solid #8c8c8c;
        border-radius: 2px;
        margin: 5px;
    }
</style>