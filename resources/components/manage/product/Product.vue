<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Product List'" :data="products" :columns="product_columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="product-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form @submit.prevent="saveProduct()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Product</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body scrollable-x">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input v-model="PRODUCT.p_code" type="text" class="form-control"
                                        :class="{ 'is-invalid': PRODUCT_ERR.p_code !== null }"
                                        placeholder="Enter product code">
                                    <div class="invalid-feedback">
                                        {{ PRODUCT_ERR.p_code }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Barcode</label>
                                    <input v-model="PRODUCT.bar_code" type="text" class="form-control"
                                        :class="{ 'is-invalid': PRODUCT_ERR.bar_code !== null }"
                                        placeholder="Enter barcode">
                                    <div class="invalid-feedback">
                                        {{ PRODUCT_ERR.bar_code }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>English Name</label>
                                    <input v-model="PRODUCT.name_en" type="text" class="form-control"
                                        :class="{ 'is-invalid': PRODUCT_ERR.name_en !== null }"
                                        placeholder="Enter english name">
                                    <div class="invalid-feedback">
                                        {{ PRODUCT_ERR.name_en }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Chinese Name</label>
                                    <input v-model="PRODUCT.name_ch" type="text" class="form-control"
                                        :class="{ 'is-invalid': PRODUCT_ERR.name_ch !== null }"
                                        placeholder="Enter chinese name">
                                    <div class="invalid-feedback">
                                        {{ PRODUCT_ERR.name_ch }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select v-model="PRODUCT.id_category" class="form-control">
                                        <option :value="null">---none---</option>
                                        <option v-for="category in categories" :key="category.id_category"
                                            :value="category.id_category">
                                            {{ `${category.name}` }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Brand</label>
                                    <select v-model="PRODUCT.id_brand" class="form-control">
                                        <option :value="null">---none---</option>
                                        <option v-for="brand in brands" :key="brand.id_brand" :value="brand.id_brand">
                                            {{ `${brand.name}` }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <select v-model="PRODUCT.id_unit" class="form-control">
                                        <option :value="null">---none---</option>
                                        <option v-for="unit in units" :key="unit.id_unit" :value="unit.id_unit">
                                            {{ `${unit.label} (${unit.name})` }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-bold">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <input v-model="PRODUCT.price" type="text" class="form-control"
                                            :class="{ 'is-invalid': PRODUCT_ERR.price !== null }"
                                            placeholder="Enter price">
                                        <div class="input-group-append">
                                            <span class="input-group-text text-bold">
                                                / {{ PRODUCT.id_unit !== null ? units.find(o => o.id_unit ===
                                PRODUCT.id_unit).label : '???' }}
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ PRODUCT_ERR.price }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label>Images</label>
                                        <div class="my-auto">
                                            <button :class="{ 'disabled': forbadeUser }" @click="resetImage()"
                                                type="button" class="mx-1 btn btn-sm btn-secondary"><i
                                                    class="fas fa-undo"></i></button>
                                            <input :disabled="forbadeUser" @change="onImageChanged($event)" type="file"
                                                accept=".png, .jpg, .jpeg" id="file-input" style="display: none">
                                            <label for="file-input">
                                                <a :class="{ 'disabled': forbadeUser }"
                                                    class="mx-1 btn btn-primary btn-sm" title="upload product image"><i
                                                        class="fa fa-upload"></i></a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="product-image" v-for="(image, i) in edited_images"
                                            @click="removeProcessingImage(i)">
                                            <i class="fas fa-times"></i>
                                            <img :src="image.thumbnail_url" />
                                        </div>
                                        <div class="product-image" v-for="(image, i) in PRODUCT.images"
                                            @click="removeAddingImage(i)">
                                            <i class="fas fa-times"></i>
                                            <img :src="image" />
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label>Types</label>
                                        <div class="my-auto">
                                            <button @click="onAddColorBtnClicked()" type="button"
                                                class="btn btn-sm btn-success"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div v-for="color in selected_colors" :key="color.id_code"
                                            @click="removeColor(color.id_color)" class="color-square"
                                            :style="{ backgroundColor: color.code }">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea v-model="PRODUCT.description" class="form-control"
                                        placeholder="Enter description" rows="5">
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button :disabled="forbadeUser || PRODUCT.id_unit === null" type="submit"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="color-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Color</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <CustomTable :title="'Color List'" :data="fixed_colors" :columns="color_columns" />
                </div>
            </div>
        </div>
    </div>
    <img ref="imageRef" :hidden="true" alt="Cropped Image" />
</template>
<script setup>
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import { onMounted, ref, reactive, h, computed } from 'vue';
import { useStore } from 'vuex';

import CustomTable from '../../includes/CustomTable.vue';
import CreateBtn from './CreateBtn.vue';
import RowBtn from './RowBtn.vue';
import AddColorBtn from './AddColorBtn.vue';
const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const brands = computed(() => store.state.brands);
const categories = computed(() => store.state.categories);
const units = computed(() => store.state.units);

const products = ref([]);
const product_columns = [
    {
        accessorKey: 'p_code',
        header: 'Product Code',
    },
    {
        accessorKey: 'bar_code',
        header: 'Barcode',
    },
    {
        accessorKey: 'link',
        header: 'Product Link',
        cell: (cell) => h('span', {
            class: 'user-select-all'
        }, cell.getValue()),
    },
    {
        accessorKey: 'name_en',
        header: 'Name EN',
    },
    {
        accessorKey: 'name_ch',
        header: 'Name CH',
    },
    {
        // accessorKey: 'category.name',
        accessorFn: ({ category }) => category === null ? '---' : category.name,
        header: 'Category',
    },
    {
        // accessorKey: 'brand.name',
        accessorFn: ({ brand }) => brand === null ? '---' : brand.name,
        header: 'Brand',
    },
    {
        accessorKey: 'action',
        header: () => h(CreateBtn, {
            funcs: {
                onCreateBtnClicked
            }
        }),
        cell: ({
            row
        }) => h(RowBtn, {
            product: row.original,
            funcs: {
                viewProduct,
                deleteProduct
            }
        }),
        enableSorting: false,
    }
];
const PRODUCT = reactive({
    id_product: null,
    p_code: null,
    bar_code: null,
    name_en: null,
    name_ch: null,
    price: null,
    description: null,
    id_unit: null,
    id_category: null,
    id_brand: null,
    id_colors: [],
    images: [],
});
const PRODUCT_ERR = reactive({
    p_code: null,
    bar_code: null,
    name_en: null,
    name_ch: null,
    price: null,
    description: null,
});
onMounted(async () => {
    await Promise.all([
        generateProducts(),
        generateColors(),
        store.dispatch('getUnits'),
        store.dispatch('getCategories'),
        store.dispatch('getBrands'),
    ]);
    $('#product-modal').on('hide.bs.modal', function () {
        for (var key in PRODUCT) {
            PRODUCT[key] = null;
        }

        for (var key in PRODUCT_ERR) {
            PRODUCT_ERR[key] = null;
        }
        PRODUCT.id_colors = [];
        PRODUCT.images = [];
        selected_colors.value = [];

        original_images.value = [];
        edited_images.value = [];
    });
});

const onCreateBtnClicked = async () => {
    $('#product-modal').modal('show');
};
const onRowCreated = (product) => {
    products.value.unshift(product);
    products.value = products.value.map(x => x);
};
const onRowUpdated = (product) => {
    products.value = products.value.map(x => x.id_product !== product.id_product ? x : product);
};
const onRowDeleted = (product) => {
    products.value = products.value.filter(x => x.id_product !== product.id_product);
};


async function generateProducts() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/products');
        products.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveProduct() {
    LoadingModal();
    try {
        let res = null;
        if (PRODUCT.id_product === null) {
            res = await createProduct(PRODUCT);
            onRowCreated(res.data.product);
        } else {
            PRODUCT.original_images = original_images.value;
            PRODUCT.edited_images = edited_images.value;
            res = await updateProduct(PRODUCT);
            onRowUpdated(res.data.product);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#product-modal').modal('hide');
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in PRODUCT_ERR) {
                PRODUCT_ERR[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
async function createProduct(PRODUCT) {
    try {
        const res = await axios.post('/api/manage/products/create', PRODUCT);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateProduct(PRODUCT) {
    try {
        const res = await axios.put('/api/manage/products/update', PRODUCT);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewProduct(id_product) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/products/read/' + id_product,
        );
        const product = res.data;
        const keys = ['id_product', 'p_code', 'bar_code', 'name_en', 'name_ch', 'price', 'description'];
        keys.forEach(key => PRODUCT[key] = product[key]);

        const { images, colors, category, brand, unit } = product;

        PRODUCT.id_brand = brand?.id_brand ?? null;
        PRODUCT.id_category = category?.id_category ?? null;
        PRODUCT.id_unit = unit.id_unit;

        PRODUCT.id_colors = colors.map(x => x.id_color);
        selected_colors.value = [...colors];

        original_images.value = [...images];
        edited_images.value = [...images];
        $('#product-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteProduct(id_product) {
    $swal.fire({
        title: 'Are you sure you want to delete the product?',
        html: '<pre>' + "Please make a confirmation." + '</pre>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Delete it.'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            LoadingModal();
            try {
                const res = await axios.delete(
                    '/api/manage/products/delete/' + id_product,
                );
                onRowDeleted(res.data.product);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}


const fixed_colors = computed(() => colors.value.filter(x => !PRODUCT.id_colors.includes(x.id_color)));
const selected_colors = ref([]);
const colors = ref([]);
const color_columns = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'code',
        header: 'Code',
    },
    {
        accessorKey: 'code',
        header: '#',
        cell: (cell) => h('div', {
            class: 'color-square mx-auto',
            style: { backgroundColor: cell.getValue(), color: cell.getValue() },
        }, null),
    },
    {
        accessorKey: 'action',
        header: 'Action',
        cell: ({
            row
        }) => h(AddColorBtn, {
            color: row.original,
            funcs: {
                addColor,
            }
        }),
        enableSorting: false,
    }
];
const onAddColorBtnClicked = async () => {
    $('#color-modal').modal('show');
};
async function generateColors() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/colors');
        colors.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
const addColor = (id_color) => {
    PRODUCT.id_colors.push(id_color);
    selected_colors.value.push(colors.value.find(x => x.id_color === id_color));
}
const removeColor = (id_color) => {
    PRODUCT.id_colors = PRODUCT.id_colors.filter(x => x !== id_color);
    selected_colors.value = selected_colors.value.filter(x => x.id_color !== id_color);

}

const original_images = ref([]);
const edited_images = ref([]);
const imageRef = ref(null);
const cropperRef = ref(null);

const onImageChanged = (e) => {
    const files = e.target.files;
    if (files && files.length > 0) {
        const fileName = files[0].name;
        const idxDot = fileName.lastIndexOf(".") + 1;
        const extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
        if (!(extFile == "jpg" || extFile == "jpeg" || extFile == "png")) {
            return MessageModal('success', 'Success', 'Only jpg/jpeg and png files are allowed!');
        }

        // 1. validate image size
        // const reader = new FileReader();
        // reader.onloadend = function () {
        //     var image = new Image();
        //     image.src = reader.result;
        //     image.onload = function () {
        //         if (this.width < 454 || this.height < 454) {
        //             return MessageModal('error', 'Action failed', 'Image dimension is prohibited!');
        //         }
        //         imageRef.value.src = reader.result;
        //         $('#cropper-modal').modal('show');
        //     };
        // }

        // 2. no validation
        const reader = new FileReader();
        reader.onloadend = function () {
            imageRef.value.src = reader.result;
            const imageWidth = 454;
            const imageHeight = 454;
            const finalAspectRatio = imageWidth / imageHeight;
            const imageElement = imageRef.value;
            cropperRef.value = new Cropper(imageElement, {
                aspectRatio: finalAspectRatio,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                zoomOnTouch: false,
                zoomOnWheel: false,
                responsive: false,
                movable: false,
                zoomable: false,
                scalable: false,
                center: true,
                autoCropArea: 1,
                viewMode: 0,
                // preview: '.preview',
                ready: () => {
                    const cropped = cropperRef.value.getCroppedCanvas({
                        width: 454,
                        height: 454,
                    });
                    PRODUCT.images.push(cropped.toDataURL());
                    cropperRef.value.destroy();
                    cropperRef.value = null;
                },
            });
        }
        reader.readAsDataURL(files[0]);
        e.target.value = null;

    }
}
const removeAddingImage = (index) => {
    PRODUCT.images.splice(index, 1);
}
const removeProcessingImage = (index) => {
    edited_images.value.splice(index, 1);
}
const resetImage = () => {
    PRODUCT.images = [];
    edited_images.value = [...original_images.value];
}
</script>
