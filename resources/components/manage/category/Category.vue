<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Categories</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Home</router-link></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <CustomTable :title="'Category List'" :data="categories" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="category-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveCategory()">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Category</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="obj.name" type="text" class="form-control"
                                :class="{ 'is-invalid': errObj.name !== null }" placeholder="Enter name">
                            <div class="invalid-feedback">
                                {{ errObj.name }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button :disabled="forbadeUser" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, reactive, h } from 'vue';
import { useStore } from 'vuex';

import CustomTable from '../../includes/CustomTable.vue';
import CreateBtn from './CreateBtn.vue';
import RowBtn from './RowBtn.vue';
const store = useStore();
const forbadeUser = store.getters.forbadeUser;

const categories = ref([]);
const columns = [
    {
        accessorKey: 'name',
        header: 'Name',
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
            category: categories.value[row.index],
            funcs: {
                viewCategory,
                deleteCategory
            }
        }),
        enableSorting: false,
    }
];
const obj = reactive({
    id_category: null,
    name: null,
});
const errObj = reactive({
    name: null,
});
onMounted(async () => {
    await generateCategories();
    $('#category-modal').on('hide.bs.modal', function () {
        obj.id_category = null;
        obj.name = null;

        for (var key in errObj) {
            errObj[key] = null;
        }
    });
});

const onCreateBtnClicked = async () => {
    $('#category-modal').modal('show');
};
const onRowCreated = (category) => {
    categories.value.unshift(category);
    categories.value = categories.value.map(obj => obj);
};
const onRowUpdated = (category) => {
    categories.value = categories.value.map(obj => obj.id_category !== category.id_category ? obj : category);
};
const onRowDeleted = (category) => {
    categories.value = categories.value.filter(obj => obj.id_category !== category.id_category);
};


async function generateCategories() {
    LoadingModal();
    try {
        const res = await axios.get('/api/manage/categories');
        categories.value = res.data;
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function saveCategory() {
    LoadingModal();
    try {
        let res = null;
        if (obj.id_category === null) {
            res = await createCategory(obj);
            onRowCreated(res.data.category);
        } else {
            res = await updateCategory(obj);
            onRowUpdated(res.data.category);
        }
        MessageModal('success', 'Success', res.data.message);
        $('#category-modal').modal('hide');
    } catch (error) {
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in errObj) {
                errObj[key] = errors[key] !== undefined ? errors[key][0] : null;
            }
            return CloseModal();
        }
        return ErrorModal(error);
    }
}
async function createCategory(obj) {
    try {
        const res = await axios.post('/api/manage/categories/create', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function updateCategory(obj) {
    try {
        const res = await axios.put('/api/manage/categories/update', obj);
        return res;
    } catch (error) {
        throw error;
    }
}
async function viewCategory(id_category) {
    LoadingModal();
    try {
        const res = await axios.get(
            '/api/manage/categories/read/' + id_category,
        );
        const category = res.data;
        const keys = ['id_category', 'name'];
        keys.forEach(key => obj[key] = category[key]);
        $('#category-modal').modal('show');
        CloseModal();
    } catch (error) {
        ErrorModal(error);
    }
}
async function deleteCategory(id_category) {
    $swal.fire({
        title: 'Are you sure you want to delete the category?',
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
                    '/api/manage/categories/delete/' + id_category,
                );
                onRowDeleted(res.data.category);
                MessageModal('success', 'Success', res.data.message);
            } catch (error) {
                ErrorModal(error);
            }
        }
    });
}
</script>
