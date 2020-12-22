<template>
    <div class="card">
        <div class="pt-5 pb-5">
            <div class="products">
                <h1>Datatable Product</h1>
                <div class="tableFilters mb-5">
                    <a href="#" @click.prevent="toggleCreationForm" class="btn btn-outline-secondary">
                        {{ creating.active ? 'Trở lại' : 'Thêm mới' }}
                    </a>
                    <div class="float-right ml-5">
                        <div class="select">
                            <select v-model="tableData.length" @change="getProducts()" class="form-control">
                                <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                            </select>
                        </div>
                    </div>
                    <input class="form-control float-right" type="text" v-model="tableData.search" placeholder="Tìm kiếm" @input="getProducts()">
                </div>
                <div class="mb-5" v-if="creating.active">
                    <form action="#" class="form-horizontal" @submit.prevent="store">
                        <div class="form-group">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Tên sản phẩm" v-model="creating.form.name">
                                <span class="invalid-feedback" v-if="creating.errors.name">
                                    <strong>{{ creating.errors.name[0] }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Mô tả" v-model="creating.form.description">
                                <span class="invalid-feedback" v-if="creating.errors.décription">
                                    <strong>{{ creating.errors.décription[0] }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <input type="file" @change="imageSelected" style="display:none" id="customFile">
                                <label class="form-control" for="customFile">Chọn ảnh</label>
                                <div v-if="uploading.imagepreview" class="mt-3">
                                    <img :src="uploading.imagepreview" class="figure-img img-fluid rounded" style="max-height:100px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Create</button>
                        </div>
                    </form>
                </div>
                <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
                    <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td>{{product.name}}</td>
                            <td>{{product.description}}</td>
                            <td>
                                <div v-bind:style="'background:url('+product.image+')center center / cover; width:150px; height:150px; position: relative;'"></div>
                            </td>
                            <td>{{product.created}}</td>
                            <td>
                                <button class="btn btn-primary" @click.prevent="editProduct(product.id)">Sửa</button>
                                <button class="btn btn-danger mt-1" @click.prevent="deleteproduct(product.id)">Xóa</button>
                            </td>
                        </tr>
                    </tbody>
                </datatable>
                <pagination :pagination="pagination" @prev="getProducts(pagination.prevPageUrl)" @next="getProducts(pagination.nextPageUrl)"></pagination>
            </div>
            <div class="container position-fixed" style="height:100vh;top:0;background:#00000085;" v-if="updating.show">
                <div class="col-md-6 offset-md-3">
                    <form class="form-horizontal form-update" @submit.prevent="updateProduct(updating.form.id)">
                        <h3>Sửa sản phẩm</h3>
                        <div class="form-group">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Tên sản phẩm" v-model="updating.form.name">
                                <span class="invalid-feedback" v-if="updating.errors.name">
                                    <strong>{{ updating.errors.name[0] }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Mô tả" v-model="updating.form.description">
                                <span class="invalid-feedback" v-if="updating.errors.description">
                                    <strong>{{ updating.errors.description[0] }}</strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <input type="file" @change="imageSelected" style="display:none;" id="customFile">
                                <label class="form-control" for="customFile">Chọn ảnh</label>
                                <div v-if="!uploading.check" class="mt-3">
                                    <img :src="updating.form.image" class="figure-img img-fluid rounded" style="max-height:100px;">
                                </div>
                                <div v-if="uploading.imagepreview" class="mt-3">
                                    <img :src="uploading.imagepreview" class="figure-img img-fluid rounded" style="max-height:100px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Lưu</button>
                            <button class="btn btn-danger" @click.prevent="showUpdateForm">Thoát</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Datatable from "./Datatable";
import Pagination from "./Pagination";

export default {
    created() {
        this.getProducts();
    },

    components: {
        Pagination,
        Datatable
    },

    data() {
        let sortOrders = {};

        let columns = [
            { width: "20%", label: "Tên", name: "name" },
            { width: "25%", label: "Mô tả", name: "category" },
            { width: "20%", label: "Hình ảnh", name: "image" },
            { width: "25%", label: "Ngày tạo", name: "created" },
            { width: "10%", label: "" },
        ];

        columns.forEach(column => {
            sortOrders[column.name] = -1;
        });

        return {
            products: [],
            columns: columns,
            sortKey: "deadline",
            sortOrders: sortOrders,
            perPage: ["10", "25", "50"],
            tableData: {
                draw: 0,
                length: 10,
                search: "",
                column: 0,
                dir: "desc"
            },
            pagination: {
                lastPage: "",
                currentPage: "",
                total: "",
                lastPageUrl: "",
                nextPageUrl: "",
                prevPageUrl: "",
                from: "",
                to: ""
            },
            creating: {
                active: false,
                form: {},
                errors: []
            },
            updating: {
                show: false,
                form: {},
                errors: {}
            },
            uploading: {
                check: false,
                image: null,
                imagepreview: null,
            },
            
        };
    },

    methods: {
        toggleCreationForm() {
            this.creating.active = !this.creating.active
            this.uploading.image = {}
        },
        showUpdateForm() {
            this.updating.show = !this.updating.show;
            this.uploading.check = false
            this.uploading.imagepreview = null
            this.uploading.image = null
            this.updating.form = null
        },
        imageSelected(e) {
            this.uploading.image = e.target.files[0];
            this.uploading.check = true
            let reader = new FileReader();
            reader.readAsDataURL(this.uploading.image);
            reader.onload = e => {
                this.uploading.imagepreview = e.target.result;
            };
        },
        getProducts(url = "/products") {
            this.tableData.draw++;
            axios
                .get(url, { params: this.tableData })
                .then(response => {
                    let data = response.data;
                    if (this.tableData.draw == data.draw) {
                        this.products = data.data.data;
                        this.configPagination(data.data);
                    }
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        store() {
            let data = new FormData
            data.append('image', this.uploading.image)
            data.append('name', this.creating.form.name)
            data.append('description', this.creating.form.description)
            console.log(data)
            axios.post('/products', data)
                .then(response => {
                    this.creating.active = false
                    this.creating.form = {}
                    this.creating.errors = []
                    this.getProducts()
                }).catch(error => {
                    if (error.response.status == 422) {
                        this.creating.errors = error.response.data.errors
                    }
                });
        },
        editProduct(id) {
            axios.get('/products/' + id)
                .then(( response ) => {
                    this.updating.form = response.data.data
                    this.updating.check = false
                    this.updating.image = {}
                    this.updating.show = !this.updating.show
                }).catch(() => {

                })
        },
        updateProduct(id) {
            let data = new FormData;
            if (this.uploading.image) { data.append('image', this.uploading.image) }
            else { data.append('image', this.updating.form.image) }
            data.append('name', this.updating.form.name)
            data.append('description', this.updating.form.description)
            
            axios.post('/update/' + id, data)
                .then(response => {
                    this.updating.form = {}
                    this.updating.show = false
                    this.uploading.image = {}
                    this.getProducts()
                }).catch(error => {
                if (error.response.status == 422) {
                    this.errors = error.response.data.errors;
                }
            });
        },
        deleteproduct(id) {
            axios.delete('/products/' + id).then(() => {
                this.getProducts()
            }).catch((error) => {
            })
        },
        configPagination(data) {
            this.pagination.lastPage = data.last_page;
            this.pagination.currentPage = data.current_page;
            this.pagination.total = data.total;
            this.pagination.lastPageUrl = data.last_page_url;
            this.pagination.nextPageUrl = data.next_page_url;
            this.pagination.prevPageUrl = data.prev_page_url;
            this.pagination.from = data.from;
            this.pagination.to = data.to;
        },
        sortBy(key) {
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, "name", key);
            this.tableData.dir = this.sortOrders[key] === 1 ? "asc" : "desc";
            this.getProducts();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value);
        }
    }
};
</script>
<style scoped>
h1 {
    text-align: center;
    font-size: 30px;
    margin-bottom: 80px;
}
.form-update {
    background: #fff;
    padding: 50px 75px 30px 75px;
    border-radius: 20px;
    width: 35%;
    position: fixed;
    top: 10%;
    z-index: 1000;
}
</style>