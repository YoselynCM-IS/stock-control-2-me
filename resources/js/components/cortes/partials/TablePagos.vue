<template>
    <div>
        <b-table v-if="remdepositos.length > 0"
            :items="remdepositos" :fields="fieldsPagos" responsive>
            <template slot="pago" slot-scope="row">
                ${{ row.item.pago | formatNumber }}
            </template>
            <template v-if="showTitle" slot="thead-top" slot-scope="row">
                <tr>
                    <th colspan="5" class="text-center">Pagos</th>
                </tr>
            </template>
            <template v-if="role_id == 6" slot="actions" slot-scope="row">
                <b-button pill size="sm" variant="primary" @click="movePago(row.item)">
                    <i class="fa fa-exchange"></i>
                </b-button>
                <b-button pill size="sm" variant="warning" @click="editPago(row.item)">
                    <i class="fa fa-pencil"></i>
                </b-button>
                <b-button pill size="sm" variant="danger" @click="deletePago(row.item)">
                    <i class="fa fa-close"></i>
                </b-button>
            </template>
        </b-table>
        <alert-v-component v-else :text="'pagos'"></alert-v-component>

        <!-- MODALAS -->
        <!-- Mover pago de corte -->
        <b-modal ref="show-move-pago" hide-footer size="sm" title="Mover pago">
            <select-corte-pagos-component :form="form" :options="options" :move="true"
                :cortes="cortes" @pagosGuardados="pagosGuardados"></select-corte-pagos-component>
        </b-modal>
        <!-- Editar pago -->
        <b-modal ref="show-editar-pago" hide-footer size="sm" title="Editar pago">
            <edit-pago-component :form="formPago" @updPayment="updPayment"></edit-pago-component>
        </b-modal>
        <!-- Eliminar pago -->
        <b-modal ref="show-eliminar-pago" hide-footer size="sm" title="Eliminar pago">
            <div class="text-center">
                <p>¿Estas seguro de eliminar el pago?</p>
                <b-button pill variant="danger" @click="confirmDelete()">
                    <i class="fa fa-close"></i> Confirmar
                </b-button>
            </div>
        </b-modal>
    </div>
</template>

<script>
import formatNumber from '../../../mixins/formatNumber';
import AlertVComponent from './AlertVComponent.vue';
import setCortes from '../../../mixins/setCortes';
import toast from '../../../mixins/toast';
import EditPagoComponent from './EditPagoComponent.vue';
export default {
    components: {AlertVComponent, EditPagoComponent},
    props: ['remdepositos', 'cortePagar', 'showTitle', 'cliente_id','role_id'],
    mixins: [formatNumber,setCortes,toast],
    data(){
        return {
            fieldsPagos: [
                {key: 'created_at', label: 'Fecha de registro'},
                'pago',
                {key: 'ingresado_por', label: 'Ingresado por'},
                'nota',
                {key: 'fecha', label: 'Fecha del pago'},
                { key: 'actions', label: '' }
            ],
            form: {
                pago_id: null,
                corte_id: null,
                corte_id_favor: null,
                cliente_id: this.cliente_id,
                total_selected: 0,
            },
            formPago: {
                id: null,
                pago: null,
                fecha: null,
                nota: null,
                corte_pagar: this.cortePagar
            },
            pago_id: null,
            options: [],
            load: false,
            cortes: []
        }
    },
    methods: {
        // MOVER PAGO
        movePago(pago){
            this.load = true;
            axios.get('/cortes/get_all').then(response => {
                this.form.pago_id = pago.id;
                this.form.corte_id = null;
                this.form.total_selected = pago.pago;
                this.cortes = response.data;
                this.options = this.setCortes(response.data, null);
                this.$refs['show-move-pago'].show();
                this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        // PAGO MOVIDO
        pagosGuardados(respuesta){
            this.$refs['show-move-pago'].hide();
            if(respuesta) {
                swal("OK", "El pago se movio correctamente.", "success")
                .then((value) => {
                    location.href = `/cortes/details_cliente/${this.form.cliente_id}`;
                });
            } else {
                this.makeToast('warning', 'Vuelve a elegir el pago.');
            }
        },
        // EDITAR PAGO
        editPago(pago) {
            this.formPago.id = pago.id;
            this.formPago.pago = pago.pago;
            this.formPago.fecha = pago.fecha;
            this.formPago.nota = pago.nota;
            this.$refs['show-editar-pago'].show();
        },
        // PAGO ACTUALIZADO
        updPayment(pago){
            this.$refs['show-editar-pago'].hide();
            swal("OK", "El pago se actualizo correctamente.", "success")
                .then((value) => {
                    location.href = `/cortes/details_cliente/${this.cliente_id}`;
                });
        },
        // ELIMINAR PAGO
        deletePago(pago){
            this.pago_id = pago.id;
            this.$refs['show-eliminar-pago'].show();
        },
        // CONFIRMAR PARA BORRAR EL PAGO
        confirmDelete(){
            this.load = true;
            axios.delete('/cortes/delete_payment', {params: {pago_id: this.pago_id}}).then(response => {
                this.$refs['show-eliminar-pago'].hide();
                swal("OK", "El pago se elimino correctamente.", "success")
                    .then((value) => {
                        location.href = `/cortes/details_cliente/${this.cliente_id}`;
                    });
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

</style>