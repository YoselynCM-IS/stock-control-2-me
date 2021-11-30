<template>
    <div>
        <div v-if="!load">
            <!-- FUNCIONES (ENCABEZADO) -->
            <b-row>
                <b-col>
                    <b-pagination v-model="currentPage" :per-page="perPage"
                        :total-rows="remclientes.length" aria-controls="table-mov">
                    </b-pagination>
                </b-col>
                <b-col>
                    <!-- BUSCAR CLIENTE -->
                    <b-input v-model="queryCliente" @keyup="mostrarClientes()"
                        style="text-transform:uppercase;" placeholder="BUSCAR CLIENTE">
                    </b-input>
                    <div class="list-group" v-if="clientes.length" id="listP">
                        <a href="#" v-bind:key="i" class="list-group-item list-group-item-action" 
                            v-for="(cliente, i) in clientes" @click="selectCliente(cliente)">
                            {{ cliente.name }}
                        </a>
                    </div>
                </b-col>
            </b-row>
            <!-- TABLA DE REMCLIENTES -->
            <b-table :items="remclientes" :fields="fieldsRClientes"
                    :per-page="perPage" :current-page="currentPage">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion | formatNumber }}</template>
                <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar | formatNumber }}</template>
                <template slot="total_pagos" slot-scope="row">${{ row.item.total_pagos | formatNumber }}</template>
            </b-table>
        </div>
        <load-component v-else></load-component>
    </div>
</template>

<script>
import formatNumber from '../../../mixins/formatNumber';
import searchCliente from '../../../mixins/searchCliente';
import LoadComponent from '../../cortes/partials/LoadComponent.vue';
export default {
    components: { LoadComponent },
    mixins: [formatNumber, searchCliente],
    data(){
        return {
            load: false,
            remclientes: [],
            fieldsRClientes: [
                {key: 'index', label: 'N.'},
                {key: 'name', label: 'Cliente'}, 
                {key: 'total', label: 'Salida'}, 
                {key: 'total_pagos', label: 'Pagado'},
                {key: 'total_devolucion', label: 'Devolución'}, 
                {key: 'total_pagar', label: 'Pagar'}
            ],
            perPage: 20,
            currentPage: 1,
        }
    },
    created: function(){
        this.getResults();
    },
    methods: {
        // OBTENER LISTA DE REMCLIENTES
        getResults(){
            this.load = true;
            axios.get('/remcliente/get_gralcortes').then(response => {
                this.remclientes = response.data;
                this.load = false;   
            }).catch(error => {
                this.load = false;
            });
        },
        // OBTENER CLIENTE
        selectCliente(cliente){
            this.load = true;
            this.clientes = [];
            axios.get('/remcliente/gc_bycliente', {params: { cliente_id: cliente.id }}).then(response => {
                this.remclientes = [];
                this.remclientes.push(response.data);
                this.queryCliente = cliente.name;
                this.load = false;   
            }).catch(error => {
                this.load = false;
            });
        }
    }
}
</script>

<style>
    #listaP{
        position: absolute;
        z-index: 100
    }
</style>