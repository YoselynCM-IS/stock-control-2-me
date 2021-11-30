<template>
    <div>
        <!-- FUNCIONES (ENCABEZADO) -->
        <b-row class="mb-3">
            <b-col><h6><b>Temporada {{ corte.tipo }}</b>: {{corte.inicio}} / {{corte.final}}</h6></b-col>
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
            <b-col sm="2" class="text-center">
                <b-button variant="secondary" pill :href="`/manager/cortes`">
                    <i class="fa fa-arrow-left"></i> Regresar
                </b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <!-- PAGINACIÓN -->
                <b-pagination v-model="currentPage" :total-rows="ctsclientes.length"
                    :per-page="perPage" aria-controls="my-table"
                    v-if="clientes.length > 0">
                </b-pagination>
            </b-col>
        </b-row>
        <!-- TABLA DE CLIENTES -->
        <div v-if="!load">
            <b-table :items="ctsclientes" :fields="fieldsClientes" responsive
                :per-page="perPage" :current-page="currentPage" id="my-table">
                <template slot="total" slot-scope="row">
                    ${{ row.item.total | formatNumber }}
                </template>
                <template slot="total_devolucion" slot-scope="row">
                    ${{ row.item.total_devolucion | formatNumber }}
                </template>
                <template slot="total_pagos" slot-scope="row">
                    ${{ row.item.total_pagos | formatNumber }}
                </template>
                <template slot="total_pagar" slot-scope="row">
                    ${{ row.item.total_pagar | formatNumber }}
                </template>
                <template slot="total_favor" slot-scope="row">
                    <div v-if="row.item.total_favor > 0">
                        ${{ row.item.total_favor | formatNumber }} <br>
                        Temp {{ row.item.corte_id_favor }}
                    </div>
                    <label v-else>N/A</label>
                </template>
                <template slot="show_details" slot-scope="row">
                    <b-button pill size="sm" variant="info" @click="row.toggleDetails">
                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}}
                    </b-button>
                </template>
                <template #row-details="row">
                    <b-row>
                        <b-col>
                            <table-remisiones :remisiones="row.item.remisiones" :showTitle="true"></table-remisiones>
                        </b-col>
                        <b-col>
                            <table-pagos :remdepositos="row.item.remdepositos" :showTitle="true"></table-pagos>
                        </b-col>
                    </b-row>
                </template>
            </b-table>
        </div>
        <load-component v-else></load-component>
    </div>
</template>

<script>
import formatNumber from '../../mixins/formatNumber';
import LoadComponent from '../funciones/LoadComponent.vue';
import searchCliente from '../../mixins/searchCliente';
import toast from '../../mixins/toast';
import AlertVComponent from './partials/AlertVComponent.vue';
import TableRemisiones from './partials/TableRemisiones.vue';
import TablePagos from './partials/TablePagos.vue';
export default {
  components: { LoadComponent, AlertVComponent, TableRemisiones, TablePagos },
    mixins: [formatNumber,searchCliente,toast],
    props: ['corte'],
    data() {
        return {
            load: false,
            ctsclientes: [],
            fieldsClientes: [
                { key: 'cliente', label: 'Cliente' },
                { key: 'total', label: 'Total' },
                { key: 'total_devolucion', label: 'Devolución' },
                { key: 'total_pagos', label: 'Pagos' },
                { key: 'total_pagar', label: 'Pagar' },
                { key: 'total_favor', label: 'A favor' },
                { key: 'show_details', label: 'Remisiones / Depósitos' }
            ],
            currentPage: 1,
            perPage: 20,
        }
    },
    created: function(){
        this.getResults();
    },
    methods: {
        // OBTENER CLIENTES DEL CORTE CON SUS REMISIONES
        getResults(){
            this.load = true;
            axios.get('/cortes/show', {params: {corte_id: this.corte.id}}).then(response => {
                this.ctsclientes = response.data;
                this.load = false;
            }).catch(error => {
                this.load = false;
                this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
            });
        },
        // MOSTRAR REMISIONES DEL CORTE DE UN CLIENTE
        selectCliente(cliente){
            this.load = true;
            axios.get('/cortes/show_bycliente', {params: {corte_id: this.corte.id, cliente_id: cliente.id}}).then(response => {
                if(response.data.length > 0){
                    this.ctsclientes = response.data;
                    this.queryCliente = cliente.name;
                } else {
                    this.makeToast('warning', `${cliente.name} No cuenta con remisiones en el corte.`);
                }
                this.clientes = [];
                this.load = false;
            }).catch(error => {
                this.load = false;
                this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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