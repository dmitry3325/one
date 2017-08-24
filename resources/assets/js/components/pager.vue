<template>
    <nav aria-label="pagination">
        <ul v-if="total>1" class="pagination" :class="(align)?'justify-content-'+align:''">
            <li class="page-item">
                <a class="page-link" @click="changePage(current-1)" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span v-if="!shotArrows" class="sr-only">Туда</span>
                </a>
            </li>
            <li v-for="page in pager" class="page-item"
                :class="((page.num == current)?'active':'')+' '+((page.disabled)?'disabled':'')">
                <a class="page-link" @click="changePage(page.num)" href="#">{{page.num}}</a>
            </li>
            <li class="page-item">
                <a class="page-link" @click="changePage(current+1)" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span v-if="!shotArrows" class="sr-only">Сюда</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    module.exports = Vue.extend({
        props: {
            'total': {
                'default': 0
            },
            'curPage': {
                'default': 1
            },
            'align': {
                'default': null,
            },
            'shotArrows': {
                'default': false
            },
            'change': {
                'default': null
            }
        },
        data: function () {
            return {
                'buttons_count': 7,
                'current': null,
                'pager': []
            };
        },
        mounted: function () {
            if (!this.curPage) {
                this.current = this.curPage;
            } else if (Url.get('page')) {
                this.current = Url.get('page');
            }
            this.buildPager();
        },
        methods: {
            buildPager() {
                this.pager = [];
                if (this.total <= this.buttons_count) {
                    for (let i = 0; i < this.total; i++) {
                        this.addPageButton(i + 1);
                    }
                } else {
                    this.addPageButton(1);
                    if (this.current < this.buttons_count - 3) {
                        for (let i = 2; i <= this.buttons_count - 2; i++) {
                            this.addPageButton(i);
                        }
                        this.addPageButton(-1);
                    } else if (this.current > this.total - (this.buttons_count - 3)) {
                        this.addPageButton(-1);
                        for (let i = this.total - (this.buttons_count - 3); i < this.total; i++) {
                            this.addPageButton(i);
                        }
                    } else {
                        this.addPageButton(-1);
                        let cC = this.buttons_count - 4;

                        let start = this.current - Math.floor(cC % 2);
                        let end = this.current + Math.floor(cC % 2);
                        if (cC % 2 === 0) {
                            start = this.current - Math.floor(cC % 2) - 2;
                            end = this.current + Math.floor(cC % 2) + 1;
                        }

                        for (let i = start; i <= end; i++) {
                            this.addPageButton(i);
                        }
                        this.addPageButton(-1);
                    }
                    this.addPageButton(this.total);
                }
            },
            addPageButton(num) {
                let page = {
                    'num': num
                };
                if (num === -1) {
                    page.num = '...';
                    page.disabled = true;
                }
                if (num === this.current) {
                    page.active = true;
                }
                this.pager.push(page);
            },

            changePage(page) {
                if (page < 1 || page > this.total || page === this.current) {
                    return false;
                }

                this.$set(this, 'current', page);
                this.buildPager();

                if (typeof this.change === 'function') {
                    this.change(page);
                }
            }
        }
    });
</script>
