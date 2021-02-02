<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <b-pagination v-model="currentPage" pills 
                    :per-page="perPage" :total-rows="folios.length">
                </b-pagination>
            </b-col>
            <b-col>
                <b-form-datepicker 
                    v-model="fecha" @context="onContext" locale="es">
                </b-form-datepicker>
                <b-row v-if="fecha !== null" class="mt-1">
                    <b-col>
                        <b-input v-model="abono" placeholder="Ingresar abono"></b-input>
                    </b-col>
                    <b-col sm="1">
                        <b-button variant="primary" pill @click="byDateAbono()" 
                        :disabled="abono < 1" size="sm">
                            <i class="fa fa-search"></i>
                        </b-button>
                    </b-col>
                </b-row>
            </b-col>
            <b-col v-if="role != 'capturist'" class="text-right">
                <b-button variant="success" pill @click="modalShow = !modalShow">
                    <b-icon-plus-circle></b-icon-plus-circle> Subir depósitos
                </b-button>
            </b-col>
        </b-row>
        <b-table v-if="folios.length > 0" :items="folios" :fields="fields"
            :per-page="perPage" :current-page="currentPage" :busy="load">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(abono)="data">
                ${{ data.item.abono | numeral('0,0') }}
            </template>
            <template v-slot:cell(saldo)="data">
                ${{ data.item.saldo | numeral('0,0') }}
            </template>
            <template v-slot:cell(occupied)="data">
                <b-badge v-if="data.item.occupied" variant="success">
                    <b-icon-check2-circle></b-icon-check2-circle>
                </b-badge>
                <b-badge v-else variant="secondary">
                    <b-icon-x-circle-fill></b-icon-x-circle-fill>
                </b-badge>
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Cargando...</strong>
                </div>
            </template>
        </b-table>
        <b-alert v-else show variant="dark" class="text-center">
            <b-icon-info-circle></b-icon-info-circle> Aun no se suben folios
        </b-alert>
        <b-modal v-model="modalShow" hide-footer title="Subir depósitos">
            <b-alert v-if="errorFormat" show variant="warning">
                <b-icon-info-circle></b-icon-info-circle> Formato de archivo no permitido
            </b-alert>
            <form @submit="onSubmit" enctype="multipart/form-data">
                <input 
                    :disabled="load" type="file" required id="archivoType"
                    class="custom-file" v-on:change="fileChange">
                <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label> <br>
                <label v-if="file"><b>Archivo:</b> {{ file ? file.name : '' }}</label>
                <b-row>
                    <b-col>
                        <b-alert v-if="load" show variant="info">
                            No cierres este recuadro hasta que el archivo termine de cargar
                        </b-alert>
                    </b-col>
                    <b-col sm="4">
                        <b-button pill :disabled="load || file == null" class="mt-3" variant="success" type="submit">
                            <i v-if="!load" class="fa fa-plus-circle"></i>
                            <b-spinner v-else type="grow" small></b-spinner>
                            {{ !load ? 'Guardar':'Cargando' }}
                        </b-button>
                    </b-col>
                </b-row>
            </form>
        </b-modal>
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
export default {
    props: ['registers', 'role'],
    data(){
        return {
            modalShow: false,
            load:false,
            file: null,
            errorFormat: null,
            folios: this.registers,
            fields: [
                {key: 'index', label: 'N.'}, 
                'fecha', 'concepto', 'abono', 'saldo',
                {key: 'occupied', label: 'Registrado'},
                {key: 'created_at', label: 'Se subio el:'}
            ],
            currentPage: 1,
            perPage: 25,
            fecha: null,
            abono: null
        }
    },
    methods: {
        byDateAbono(){
            if(this.abono !== null){
                this.load = true;
                axios.get('/folios/by_date_abono', {params: {fecha: this.fecha, abono: this.abono}}).then(response => {
                    this.folios = response.data;
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                });
            }
        },
        fileChange(e){
            this.file = e.target.files[0];
        },
        onContext(ctx) {
            if(this.fecha !== null){
                this.load = true;
                axios.get('/folios/by_date', {params: {fecha: this.fecha}}).then(response => {
                    this.folios = response.data;
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                });
            }
        },
        onSubmit(e){
            e.preventDefault();

            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.csv)$/i;
            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                this.errorFormat = false;
                let formData = new FormData();
                formData.append('file', this.file);

                axios.post('/folios/load_folios', formData, { headers: { 'content-type': 'multipart/form-data' } })
                .then(response => {
                    this.folios = response.data;
                    this.modalShow = false;
                    swal("Guardado", "Los depósitos se guardaron correctamente", "success");
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    swal("Error", "Ocurrio un error al subir los depósitos.", "error");
                });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        }
    }
}
</script>