<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <router-link :to="{ name: 'dashboard' }" class="brand-link">
            <img :src="logoImage" alt="" class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-light">Phnom Penh Duty Free Store</span>
        </router-link>

        <div class="sidebar">

            <router-link :to="{ name: 'profile' }">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img :src="default5x5Image" class="img-circle elevation-2" alt="">
                    </div>
                    <div class="info">
                        <a class="d-block">{{ user.name }}</a>
                    </div>
                </div>
            </router-link>


            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item">
                        <router-link :to="{ name: 'dashboard' }" active-class="active" class="nav-link">
                            <!-- <i class="nav-icon fas fa-th"></i> -->
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.products')" class="nav-item">
                        <router-link :to="{ name: 'manage.products' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-gifts"></i>
                            <p>
                                Products
                            </p>
                        </router-link>
                    </li>
                    <!-- <li v-if="validUser('manage.scales')" class="nav-item">
                        <router-link :to="{ name: 'manage.scales' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-ruler-combined"></i>
                            <p>
                                Dimension Scales
                            </p>
                        </router-link>
                    </li> -->
                    <li v-if="validUser('manage.units')" class="nav-item">
                        <router-link :to="{ name: 'manage.units' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>
                                Item Uits
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.brands')" class="nav-item">
                        <router-link :to="{ name: 'manage.brands' }" active-class="active" class="nav-link">
                            <i class="nav-icon fab fa-font-awesome-flag"></i>
                            <p>
                                Brands
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.categories')" class="nav-item">
                        <router-link :to="{ name: 'manage.categories' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-sort-alpha-down"></i>
                            <p>
                                Categories
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.colors')" class="nav-item">
                        <router-link :to="{ name: 'manage.colors' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-palette"></i>
                            <p>
                                Colors
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.users')" class="nav-item">
                        <router-link :to="{ name: 'manage.users' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Users
                            </p>
                        </router-link>
                    </li>
                    <li v-if="validUser('manage.backgrounds')" class="nav-item">
                        <router-link :to="{ name: 'manage.backgrounds' }" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-image"></i>
                            <p>
                                Background
                            </p>
                        </router-link>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { computed, onMounted } from 'vue';
import logoImage from "../../../public/assets/images/logo.jpg";
import default5x5Image from "../../../public/assets/images/default5x5.png";

const router = useRouter();

const store = useStore();
const user = computed(() => store.state.user);
const permittedUser = store.getters.permittedUser;

function validUser(routeName) {
    return permittedUser(getRouteIDPerms(routeName));
}

function getRouteIDPerms(routeName) {
    const id_perms = router.resolve({ name: routeName }).meta.guard.id_perms;
    return id_perms ? id_perms : null;
};
function logout() {
    $swal.fire({
        title: 'Are you sure you want to <span class="text-danger">LOG OUT</span>?',
        icon: 'question',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes! I\'m sure.',
        showCancelButton: true,
        width: '525'
    }).then(async (sw) => {
        if (sw.isConfirmed) {
            router.push({ name: 'logout' })
        }
    });
}
</script>
