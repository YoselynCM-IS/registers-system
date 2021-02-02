<template>
    <div>
        <b-row v-if="registros.length > 0">
            <b-col>
                <b-pagination class="mt-1" v-model="currentPage" pills
                    :per-page="perPage" :total-rows="registros.length" :disabled="load">
                </b-pagination>
            </b-col>
            <b-col sm="2">
                 <b-button :disabled="load" variant="info" pill 
                    @click="moreSearch()">
                    <b-icon-search></b-icon-search> Búsquedas
                </b-button>
            </b-col>
            <b-col sm="3">
                <b-button :disabled="load || temporal1 == null" variant="dark" pill 
                    :href="`/registros/download/${temporal1}`">
                    <b-icon-download></b-icon-download> Descargar (Escuela)
                </b-button>
            </b-col>
            <b-col class="text-right">
                <b-button v-if="role === 'reviewer' || role === 'manager'" :disabled="load" 
                    variant="primary" pill @click="updateStatus()">
                    <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Actualizar status
                </b-button>
            </b-col>
        </b-row>
        <b-row v-if="role === 'manager'">
            <b-col>
                <b-button :disabled="load" variant="dark" pill 
                    :href="`/registros/download_status/${value_status}`">
                    <b-icon-download></b-icon-download> Descargar
                </b-button>
            </b-col>
            <b-col class="text-right">
                <b-button :disabled="load" 
                    variant="danger" pill @click="updateRejected()">
                    <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Revisar rechazados
                </b-button>
            </b-col>
        </b-row>
        <b-table v-if="registros.length > 0" class="mt-3" responsive 
            :items="registros" :fields="fields" :busy="load"
            :per-page="perPage" :current-page="currentPage">
            <template v-slot:cell(index)="data">
                {{ data.index + 1 }}
            </template>
            <template v-slot:cell(name)="data">
                {{ data.item.name }}
            </template>
            <template v-slot:cell(price)="data">
                ${{ data.item.price | numeral('0,0') }}
            </template>
            <template v-slot:cell(total)="data">
                ${{ data.item.total | numeral('0,0') }}
            </template>
            <template v-slot:cell(school)="data">
                {{ data.item.school.name }}
            </template>
            <template v-slot:cell(information)="data">
                <b-button pill variant="info" @click="showInfo(data.item)">
                    <b-icon-info-circle></b-icon-info-circle>
                </b-button>
            </template>
            <template v-slot:cell(created_at)="data">
                {{ data.item.created_at | moment("YYYY-MM-DD hh:mm:ss") }}
            </template>
            <template v-slot:cell(check)="data">
                <div v-if="data.item.check === 'accepted'" >
                    <!-- <b-badge pill variant="success">
                        <b-icon-check2-circle></b-icon-check2-circle> Aceptado
                    </b-badge> -->
                    
                    <div v-if="!data.item.book.includes('DIGITAL')">
                        <div v-if="data.item.delivery == 0">
                            <!-- <b-button class="mb-1" variant="outline-warning" pill @click="set_delivery(data.item)"
                                size="sm">
                                <i class="fa fa-exclamation-triangle"></i> Marcar entrega
                            </b-button> -->
                            <b-badge pill variant="warning">
                                <i class="fa fa-exclamation-triangle"></i> Libro no entregado
                            </b-badge>
                            <b-button v-if="data.item.validate == 'NO ENVIADO'" variant="outline-dark" 
                                pill size="sm" @click="resend_mail(data.item)">
                                <i class="fa fa-share"></i> Reenviar correo
                            </b-button>
                        </div>
                        <b-badge v-else pill variant="success">
                            <i class="fa fa-check"></i> Libro entregado
                        </b-badge>
                    </div>
                    <div v-else>
                        <b-badge v-if="data.item.codes" variant="success">
                            <i class="fa fa-check"></i> Código enviado
                        </b-badge>
                        <b-badge v-else pill variant="warning">
                            <i class="fa fa-exclamation-triangle"></i> Código no enviado
                        </b-badge>
                    </div>
                </div>
                <b-badge v-if="data.item.check === 'process'" pill variant="secondary">
                    <b-icon-three-dots></b-icon-three-dots> Proceso
                </b-badge>
                <div v-if="data.item.check === 'rejected'">
                    <b-badge pill variant="danger">
                        <b-icon-x-circle-fill></b-icon-x-circle-fill> Rechazado
                    </b-badge>
                    <b-button v-if="role === 'reviewer'" :disabled="load"
                        variant="danger" pill size="sm" @click="debugRejected(data.item)">
                        <b-icon-trash></b-icon-trash> Depurar
                    </b-button>
                    <b-button v-if="role === 'manager'" :disabled="load"
                        variant="secondary" pill size="sm" @click="selectStatus(data.item)">
                        <b-icon-arrow-clockwise></b-icon-arrow-clockwise> Actualizar
                    </b-button>
                </div>
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Actualizando...</strong>
                </div>
            </template>
        </b-table>
        <b-alert v-else show variant="dark" class="text-center">
            <b-icon-info-circle></b-icon-info-circle> Aun no hay registros
        </b-alert>
        <b-modal v-model="modalShow" hide-footer 
            title="Detalles de registro" size="xl">
            <b>Fecha de registro:</b> {{ student.created_at | moment("YYYY-MM-DD hh:mm:ss") }}<br><br>
            <b-row>
                <b-col>
                    <table class="table">
                        <tbody>
                            <tr class="table-info">
                                <th colspan="2"><h6><b>Datos de pago</b></h6></th>
                            </tr>
                        </tbody>
                        <tbody v-for="(registro, i) in student.registros" v-bind:key="i">
                            <tr class="table-ligth">
                                <th colspan="2"><h6><b>Pago: {{ i + 1 }}</b></h6></th>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Fecha de pago</th>
                                <td>{{ registro.date }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Tipo de pago</th>
                                <td>{{ registro.type }}</td>
                            </tr>
                            <tr v-if="registro.bank !== null">
                                <th class="text-right" scope="row">Banco</th>
                                <td>{{ registro.bank }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">
                                    <label v-if="registro.type === 'ventanilla'">Movimiento</label>
                                    <label v-if="registro.type === 'practicaja' || 
                                            (registro.type == 'transferencia' && registro.bank == 'BANCOMER')">
                                        Folio
                                    </label>
                                    <label v-if="registro.type == 'transferencia' && (registro.bank == null || registro.bank == 'BANCO AZTECA' || registro.bank == 'BANORTE' || registro.bank == 'HSBC' || registro.bank == 'BANBAJIO' || registro.bank == 'SANTANDER' || registro.bank == 'BANCOPPEL')">
                                        Referencia
                                    </label>
                                    <label v-if="registro.type == 'transferencia' && registro.bank == 'Referencia numerica'">
                                        Referencia numerica
                                    </label>
                                </th>
                                <td>{{ registro.invoice }}</td>
                            </tr>
                            <tr v-if="registro.type == 'practicaja'">
                                <th class="text-right" scope="row">Autorización</th>
                                <td>{{ registro.auto }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Total depositado</th>
                                <td>${{ registro.total | numeral('0,0') }}</td>
                            </tr>
                            <tr v-if="registro.type !== 'transferencia'">
                                <th class="text-right" scope="row">Lugar donde se<br>realizo el pago</th>
                                <td>{{ registro.plaza }}</td>
                            </tr>
                            <tr v-if="student.check === 'accepted' && registro.folio">
                                <th class="text-right" scope="row">Referencia</th>
                                <td>
                                    <label><b>Fecha:</b> {{ registro.folio.fecha }}</label><br>
                                    <label><b>Concepto:</b> {{ registro.folio.concepto }}</label><br>
                                    <label><b>Abono:</b> ${{ registro.folio.abono | numeral('0,0') }}</label><br>
                                    <label><b>Saldo:</b> ${{ registro.folio.saldo | numeral('0,0') }}</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <table class="table">
                        <tbody> 
                            <tr class="table-info">
                                <th colspan="2"><h6><b>Comprobante(s) del pago</b></h6></th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr v-for="(comprobante, i) in student.comprobantes" v-bind:key="i">
                                <th class="text-right" scope="row">Comprobante {{ i + 1 }}</th>
                                <td><a :href="comprobante.public_url" target="_blank">Ver</a></td>
                            </tr>
                        </tbody>
                    </table>
                </b-col>
                <b-col>
                    <table class="table">
                        <tbody>
                            <tr class="table-info">
                                <th colspan="2"><h6><b>Datos del libro</b></h6></th>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Libro</th>
                                <td>{{ student.book }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Cantidad</th>
                                <td>{{ student.quantity }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Precio</th>
                                <td>${{ student.price | numeral('0,0') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <table class="table">
                        <tbody>
                            <tr class="table-info">
                                <th colspan="2"><h6><b>Datos del alumno</b></h6></th>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Nombre</th>
                                <td>{{ student.name }}</td>
                            </tr>
                            <tr v-if="student.telephone !== null">
                                <th class="text-right" scope="row">Numero de teléfono</th>
                                <td>{{ student.telephone }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Correo</th>
                                <td>{{ student.email }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" scope="row">Escuela</th>
                                <td>{{ school.name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </b-col>
            </b-row>
        </b-modal>
        <b-modal v-model="modalShow2" hide-footer title="Buscar por:">
            <label><b>Status</b></label>
            <b-row>
                <b-col>
                    <b-form-select v-model="value_status" :options="status">
                    </b-form-select>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchStatus()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Fecha de pago</b></label>
            <b-row>
                <b-col>
                    <b-form-datepicker v-model="fecha">
                    </b-form-datepicker>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchDate()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <b>Escuela</b>
            <b-row>
                <b-col>
                    <b-form-input v-model="escuela"
                        @keyup="showSchools()" style="text-transform:uppercase;">
                    </b-form-input>
                    <div class="list-group" v-if="schools.length" id="listR">
                        <a 
                            href="#" v-bind:key="i" 
                            class="list-group-item list-group-item-action" 
                            v-for="(school, i) in schools" 
                            @click="selectSchool(school)">
                            {{ school.name }}
                        </a>
                    </div>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchSchool()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Tipo de pago:</b></label>
            <b-row>
                <b-col>
                    <b-form-select v-model="sType" :options="types"></b-form-select>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchType()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Folio / Movimiento</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sFolio"></b-form-input>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchFolio()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Total depositado</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sTotal" type="number">
                    </b-form-input>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchTotal()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Libro</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sLibro"
                    @keyup="showBooks()" style="text-transform:uppercase;">
                    </b-form-input>
                    <div class="list-group" v-if="books.length" id="listR">
                        <a 
                            href="#" v-bind:key="i" 
                            class="list-group-item list-group-item-action" 
                            v-for="(book, i) in books" 
                            @click="selectBook(book)">
                            {{ book.name }}
                        </a>
                    </div>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchBook()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row><hr>
            <label><b>Alumno</b></label>
            <b-row>
                <b-col>
                    <b-form-input v-model="sStudent"
                        @keyup="showStudents()" style="text-transform:uppercase;">
                    </b-form-input>
                    <div class="list-group" v-if="students.length" id="listR">
                        <a 
                            href="#" v-bind:key="i" 
                            class="list-group-item list-group-item-action" 
                            v-for="(student, i) in students" 
                            @click="selectStudent(student)">
                            {{ student.name }}
                        </a>
                    </div>
                </b-col>
                <b-col sm="4">
                    <b-button pill variant="primary" @click="searchStudent()">
                        <b-icon-search></b-icon-search> Buscar
                    </b-button>
                </b-col>
            </b-row>
        </b-modal>
        <b-modal v-model="modalShow3" hide-footer :title="form_std.name" size="xl">
            <search-folio @foliosSelected="foliosSelected"></search-folio>
            <hr>
            <b-form @submit.prevent="updateRegister">
                <h6><b>Asignar folios</b></h6>
                <b-table responsive :items="std_registros" :fields="fieldsReg">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(selected)="data">
                         <b-form-select :disabled="seleccionados.length == 0" 
                            v-model="data.item.folio_id" :options="seleccionados"
                        ></b-form-select>
                    </template>
                </b-table>
                <div class="text-right">
                    <b-button pill :disabled="load" variant="success" type="submit">
                        <b-icon-check2-circle></b-icon-check2-circle> Guardar
                    </b-button>
                </div>
            </b-form>
        </b-modal>
        <b-modal v-model="openConfirm" size="sm" centered title="CONFIRMAR ENTREGA A">
            <h5 style="color: #3b0069">{{ std_delivery.name }}</h5>
            <template #modal-footer>
                <b-button :disabled="load" variant="success" pill @click="save_delivery()">
                    <i v-if="!load" class="fa fa-check-circle"></i>
                    <b-spinner v-else type="grow" small></b-spinner>
                    {{ !load ? 'OK':'GUARDANDO' }}
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: ['registers', 'role'],
    data(){
        return {
            registros: this.registers,
            fields: [
                { label: 'N.', key: 'index' },
                { label: 'Escuela', key: 'school' },
                { label: 'Alumno', key: 'name' },
                { label: 'Libro', key: 'book' },
                { label: 'Cant.', key: 'quantity' },
                { label: 'Precio', key: 'price' },
                { label: 'Total', key: 'total' },
                { label: 'Fecha de registro', key: 'created_at' },
                { label: 'Pagos', key: 'information' },
                { label: 'Status', key: 'check' }
            ],
            modalShow: false,
            modalShow2: false,
            modalShow3: false,
            student: {},
            school: {},
            sFolio: null,
            sTotal: null,
            currentPage: 1,
            perPage: 25,
            escuela: '',
            schools: [],
            fecha: null,
            sType: null,
            sSchool: null,
            types: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'practicaja', text: 'DEPOSITO EN PRACTICAJA'},
                { value: 'ventanilla', text: 'DEPOSITO EN VENTILLA'},
                // { value: 'oxxo', text: 'DEPOSITO EN OXXO'},
                { value: 'transferencia', text: 'TRANSFERENCIA'}
            ],
            sLibro: '',
            books: [],
            load: false,
            temporal1: null,
            temporal2: null,
            value_status: null,
            status: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'accepted', text: 'Aceptado' },
                { value: 'rejected', text: 'Rechazado' },
                { value: 'process', text: 'Proceso' }
            ],
            sStudent: null,
            students: [],
            form: {},
            folios: [],
            seleccionados: [],
            fieldsSel: [
                { key: 'index', label: 'N.' },
                { key: 'fecha', label: 'Fecha' },
                { key: 'concepto', label: 'Concepto' },
                { key: 'abono', label: 'Abono' },
                { key: 'registro', label: 'Registro' },
                { key: 'delete', label: 'Quitar' }
            ],
            fieldsReg: [
                { key: 'index', label: 'N.' },
                { key: 'type', label: 'Tipo' },
                { key: 'bank', label: 'Banco' },
                { key: 'invoice', label: 'Referencia' },
                { key: 'total', label: 'Total' },
                { key: 'date', label: 'Fecha' },
                { key: 'selected', label: 'Asignar folio' },
                
            ],
            form_std: {},
            std_registros: [],
            std_delivery: { id: null, name: '' },
            openConfirm: false
        }
    },
    methods: {
        moreSearch(){
            this.sSchool = null;
            this.escuela = null;
            this.fecha = null;
            this.sLibro = '';
            this.temporal1 = null;
            this.temporal2 = null;
            this.modalShow2 = !this.modalShow2;
        },
        updateStatus(){
            this.load = true;
            axios.put('/registros/update_status').then(response => {
                this.registros = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        showInfo(student){
            axios.get('/student/show_registers', {params: {student_id: student.id}}).then(response => {
                this.student = response.data;
                this.school = this.student.school;
                this.modalShow = !this.modalShow;
            }).catch(error => {
                // PENDIENTE
            });
        },
        showSchools(){
            if(this.escuela.length > 0 && this.escuela !== ' '){
                axios.get('/schools/show_schools', {params: {escuela: this.escuela}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
        selectSchool(school){
            this.sSchool = school;
            this.escuela = school.name;
            this.schools = [];
        },
        searchSchool(){
            axios.get('/schools/show_school', {params: {school_id: this.sSchool.id}}).then(response => {
                if(response.data.length === 0){
                    this.makeToast(`No hay registros de ${this.sSchool.name}`);
                    this.schools = [];
                } else {
                    this.registros = response.data;
                    this.temporal1 = this.sSchool.id;
                    this.modalShow2 = false;
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchType(){
            axios.get('/registros/by_type', {params: {type: this.sType}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sType = null;
                    this.registros = response.data;
                } else {
                     this.makeToast(`No hay registros de pagos realizados en ${this.sType}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchFolio(){
            axios.get('/registros/by_folio', {params: {folio: this.sFolio}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sFolio = null;
                    this.registros = response.data;
                } else {
                    this.makeToast(`No hay registro del folio ${this.sFolio}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchTotal(){
            axios.get('/registros/by_total', {params: {total: this.sTotal}}).then(response => {
                if(response.data.length > 0){
                    this.modalShow2 = false;
                    this.sTotal = null;
                    this.registros = response.data;
                } else {
                    this.makeToast(`No hay registro con abonos de $${this.sTotal}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        showBooks(){
            if(this.sLibro.length > 0 && this.sLibro !== ' '){
                axios.get('/books/show_books', {params: {book: this.sLibro}}).then(response => {
                    this.books = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.books = [];
            }
        },
        selectBook(book){
            this.sLibro = book.name;
            this.books = [];
        },
        searchBook(){
            axios.get('/registros/by_book', {params: {book: this.sLibro}}).then(response => {
                if(response.data.length > 0){
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros del libro ${this.sLibro}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        searchDate(){
            axios.get('/registros/by_date', {params: {fecha: this.fecha}}).then(response => {
                if(response.data.length > 0){
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros de pagos realizados el ${this.fecha}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        updateRejected(){
            this.load = true;
            axios.put('/registros/update_rejected').then(response => {
                this.registros = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        makeToast(message){
            this.$bvToast.toast(message, {
                title: 'Mensaje',
                toaster: 'b-toaster-top-center',
                solid: true,
                appendToast: false
            });
        },
        debugRejected(student){
            this.load = true;
            axios.delete('/student/delete', {params: {student_id: student.id}}).then(response => {
                this.registros = response.data;
                this.makeToast('El alumno ha sido eliminado de la lista');
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });   
        },
        searchStatus(){
            this.load = true;
            axios.get('/registros/by_status', {params: {status: this.value_status}}).then(response => {
               this.registros = response.data;
               this.modalShow2 = false;
               this.load = false;
            }).catch(error => {
                this.load = false;
            });
        },
        showStudents(){
            if(this.sStudent.length > 0 && this.sStudent !== ' '){
                axios.get('/student/show_students', {params: {student: this.sStudent}}).then(response => {
                    this.students = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.students = [];
            }
        },
        searchStudent(){
            axios.get('/registros/by_student', {params: {student: this.sStudent}}).then(response => {
                if(response.data.length > 0){
                    this.sStudent = null;
                    this.registros = response.data;
                    this.modalShow2 = false;
                } else {
                     this.makeToast(`No hay registros del alumno ${this.sStudent}`);
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        selectStudent(student){
            this.sStudent = student.name;
            this.students = [];
        },
        selectStatus(student){
            axios.get('/student/show_registers', {params: {student_id: student.id}}).then(response => {
                this.form_std.student_id = student.id;
                this.form_std.name = student.name;
                this.std_registros = response.data.registros;
                this.modalShow3 = true;
            }).catch(error => {
                // PENDIENTE
            });
        },
        foliosSelected(folios){
            this.seleccionados = [];
            if(folios.length === this.std_registros.length){
                this.seleccionados.push({
                    value: null,
                    text: 'Selecciona una opción',
                    disabled: true
                });
                folios.forEach(folio => {
                    this.seleccionados.push({
                        value: folio.id,
                        text: folio.concepto
                    });
                });
            }
        },
        deleteSelect(i){
            this.seleccionados.splice(i,1);
        },
        updateRegister(){
            this.load = true;
            this.form_std.registros = this.std_registros;
            axios.put('/student/update_status', this.form_std).then(response => {
                this.registros = response.data;
                this.modalShow3 = false;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        set_delivery(register) {
            this.std_delivery.id = register.id;
            this.std_delivery.name = register.name;
            this.openConfirm = true;
        },
        save_delivery() {
            this.load = true;
            axios.put('/student/update_delivery', this.std_delivery).then(response => {
                this.registros = response.data;
                this.load = false;
                this.openConfirm = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        },
        resend_mail(student){
            this.load = true;
            axios.put('/registros/resend_mail', student).then(response => {
                this.registros = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>

<style scoped>
    #listR{
        position: absolute;
        z-index: 100;
    }
</style>