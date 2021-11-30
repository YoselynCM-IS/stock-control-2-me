<template>
    <div>
        <b-table v-if="remisiones.length > 0"
            :items="remisiones" :fields="fieldsRems"
            responsive :tbody-tr-class="rowClass">
            <template slot="total" slot-scope="row">
                ${{ row.item.total | formatNumber }}
            </template>
            <template slot="total_devolucion" slot-scope="row">
                ${{ row.item.total_devolucion | formatNumber }}
            </template>
            <template slot="total_pagos" slot-scope="row">
                ${{ row.item.pagos | formatNumber }}
            </template>
            <template slot="total_pagar" slot-scope="row">
                ${{ row.item.total_pagar | formatNumber }}
            </template>
            <template slot="actions" slot-scope="row">
                <b-button v-if="row.item.total_pagar == row.item.total" 
                    pill size="sm" variant="primary" @click="moveRemisione(row.item)">
                    <i class="fa fa-exchange"></i>
                </b-button>
            </template>
            <template v-if="showTitle" slot="thead-top" slot-scope="row">
                <tr>
                    <th colspan="6" class="text-center">Remisiones</th>
                </tr>
            </template>
        </b-table>
        <alert-v-component v-else :text="'remisiones'"></alert-v-component>

        <!-- MODALS -->
        <!-- Mover remisión de corte-->
        <b-modal ref="show-move-rem" hide-footer size="sm"
            :title="`Mover remisión ${form.remisione_id}`">
           <select-corte-component :form="form" :options="options" :move="true"
                        @remsGuardadas="remsGuardadas"></select-corte-component> 
        </b-modal>
    </div>
</template>

<script>
import formatNumber from '../../../mixins/formatNumber';
import SelectCorteComponent from '../SelectCorteComponent.vue';
import AlertVComponent from './AlertVComponent.vue';
import setCortes from '../../../mixins/setCortes';
import toast from '../../../mixins/toast';
export default {
    components: { AlertVComponent, SelectCorteComponent },
    props: ['remisiones', 'showTitle'],
    mixins: [formatNumber,setCortes,toast],
    data(){
        return {
            fieldsRems: [
                { key: 'id', label: 'Folio' },
                { key: 'fecha_creacion', label: 'Fecha de creación' },
                { key: 'total', label: 'Salida' },
                { key: 'total_devolucion', label: 'Devolución' },
                { key: 'total_pagos', label: 'Pagos' },
                { key: 'total_pagar', label: 'Pagar' },
                { key: 'actions', label: '' }
            ],
            form: {
                remisione_id: null,
                corte_id: null
            },
            options: []
        }
    },
    methods: {
        // COLOR VER A REMISIONES PAGADAS
        rowClass(item, type) {
            if (!item) return
            if (item.total_pagar == 0 && (item.pagos > 0 || item.total_devolucion > 0)) return 'table-success'
        },
        // MOVER REMISION
        moveRemisione(remision){
            this.load = true;
            axios.get('/cortes/get_all').then(response => {
                this.form.remisione_id = remision.id;
                this.form.corte_id = null;
                this.cliente_id = remision.cliente_id;
                this.options = this.setCortes(response.data, null);
                this.$refs['show-move-rem'].show();
                this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        // REMISION MOVIDA
        remsGuardadas(valor){
            this.$refs['show-move-rem'].hide();
            if(valor) {
                swal("OK", "La remisión se movio correctamente.", "success")
                .then((value) => {
                    location.href = `/cortes/details_cliente/${this.cliente_id}`;
                });
            } else this.makeToast('warning', 'Se eligió el mismo corte.');
        }
    }
}
</script>

<style>

</style>