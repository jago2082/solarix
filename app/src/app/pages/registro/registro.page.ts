import { Component, inject, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, NavController, LoadingController, AlertController } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService } from 'src/app/services/api/api.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { RegistroUsuarioRequest } from 'src/app/models/registroUsuarioRequest';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { Table } from 'primeng/table';

@Component({
  selector: 'app-registro',
  templateUrl: './registro.page.html',
  styleUrls: ['./registro.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    IonicModule,
    FormsModule,
    ReactiveFormsModule,
    HeaderComponent,
    FooterComponent,
    TableModule,
    InputTextModule
  ],
})
export class RegistroPage implements OnInit {
  private fb = inject(FormBuilder);
  form: FormGroup;
  loading = false;
  saving = false;

  @ViewChild('table') table!: Table;
  globalFilter: string = '';
  users: any[] = [];
  isModalOpen = false;
  editing = false;
  selectedId?: string | number;

  constructor(
    private _apiService: ApiService,
    private _alert: AlertService,
    private _nav: NavController,
    private _loadingCtrl: LoadingController,
    private _alertCtrl: AlertController,
    private _sesion: SessionsService
  ) {
    this.form = this.fb.group({
      nombres: ['', [Validators.required]],
      apellidos: ['', [Validators.required]],
      codigo: ['', [Validators.required]],
      telefono: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      estado: ['A', [Validators.required]]
    });
  }

  async ngOnInit() {
    if (!this._sesion.IsAdmin()) {
      this._alert.error('No tiene permisos para acceder a esta opción');
      this._nav.navigateRoot('tabs/in', { animated: true });
      return;
    }
    this.loadUsers();
  }

  loadUsers() {
    this.loading = true;
    this._apiService.getUsers().subscribe({
      next: (res: any) => {
        this.loading = false;
        this.users = Array.isArray(res) ? res.map((u: any) => ({
          id: u.id,
          nombres: u.nombres,
          apellidos: u.apellidos,
          codigo: u.codigo,
          telefono: u.telefono,
          email: u.email,
          estado: u.estado
        })) : [];
      },
      error: (err) => {
        this.loading = false;
        console.error(err);
        this._alert.error('Error al consultar usuarios');
      }
    });
  }

  aplicarFiltroGlobal(event: Event) {
    const input = event.target as HTMLInputElement;
    this.table?.filterGlobal(input.value, 'contains');
  }

  abrirModal(user?: any) {
    this.editing = !!user;
    this.selectedId = user?.id;
    this.isModalOpen = true;

    if (user) {
      this.form.patchValue({
        nombres: user.nombres,
        apellidos: user.apellidos,
        codigo: user.codigo,
        telefono: user.telefono,
        email: user.email,
        estado: user.estado || 'A',
        password: ''
      });
      this.form.get('password')?.clearValidators();
      this.form.get('password')?.updateValueAndValidity();
    } else {
      this.form.reset({
        nombres: '',
        apellidos: '',
        codigo: '',
        telefono: '',
        email: '',
        estado: 'A',
        password: ''
      });
      this.form.get('password')?.setValidators([Validators.required, Validators.minLength(6)]);
      this.form.get('password')?.updateValueAndValidity();
    }
    this.form.markAsPristine();
    this.form.markAsUntouched();
  }

  cerrarModal() {
    this.isModalOpen = false;
    this.editing = false;
    this.selectedId = undefined;
    this.form.reset();
  }

  async guardar() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const datos = this.form.getRawValue();
    const requestData: RegistroUsuarioRequest = {
      nombres: datos.nombres,
      apellidos: datos.apellidos,
      codigo: datos.codigo,
      telefono: datos.telefono,
      email: datos.email,
      estado: datos.estado || 'A'
    };

    if (!this.editing) {
      requestData.password = datos.password;
    } else if (datos.password) {
      requestData.password = datos.password;
    }

    const loading = await this._loadingCtrl.create({
      message: this.editing ? 'Actualizando...' : 'Registrando...'
    });
    await loading.present();
    this.saving = true;

    const operation = this.editing && this.selectedId
      ? this._apiService.actualizarUsuario(this.selectedId, requestData)
      : this._apiService.registrarUsuario(requestData);

    operation.subscribe({
      next: (resp: any) => {
        loading.dismiss();
        this.saving = false;
        this._alert.success(resp?.message || 'Operación exitosa');
        this.cerrarModal();
        this.loadUsers();
      },
      error: (err) => {
        loading.dismiss();
        this.saving = false;
        console.error(err);
        this._alert.error('Error en la operación');
      }
    });
  }

  async eliminar(user: any) {
    const alert = await this._alertCtrl.create({
      header: 'Confirmar eliminación',
      message: `¿Desea eliminar al usuario ${user.nombres} ${user.apellidos}?`,
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: () => this.confirmarEliminar(user)
        }
      ]
    });
    await alert.present();
  }

  confirmarEliminar(user: any) {
    this._apiService.eliminarUsuario(user.id).subscribe({
      next: (resp: any) => {
        this._alert.success(resp?.message || 'Usuario eliminado');
        this.loadUsers();
      },
      error: (err) => {
        console.error(err);
        this._alert.error('Error al eliminar usuario');
      }
    });
  }

  irALogin() {
    this._nav.navigateRoot('home', { animated: true });
  }
}
