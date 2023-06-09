<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//cargar clases
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/nombre/{nombre?}', function ($nombre = null) {
    //return view('welcome');
    $texto = "Tu nombre es : " . $nombre;
    //return $texto;
    return view('pruebas',array(
        'texto' => $texto
    ));
});
/**
 * GET -> mostrar
 * POST -> guardar
 * PUT -> actualizar
 * DELETE -> borrar
 */

Route::get('/lenguajes', 'PruebasController@index');
Route::get('/test-orm', 'PruebasController@testOrm');

//rutas del API
//Route::get('/usuario/pruebas', 'UserController@pruebas');
//Route::get('/publicar/pruebas', 'PublicarController@pruebas');

//rutas de usuario
Route::post('/api/registerUsuario', 'UserController@registerUsuario');
Route::post('/api/registerEmpresa', 'UserController@registerEmpresa');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::put('/api/user/updateEmpresa', 'UserController@updateEmpresa');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::post('/api/user/uploadcv', 'UserController@uploadcv')->middleware(ApiAuthMiddleware::class);
Route::post('/api/user/uploadFotoPublicacion', 'UserController@uploadFotoPublicacion')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}','UserController@getImage');
Route::get('/api/user/cv/{filename}','UserController@getFile');
Route::get('/api/user/foto/{filename}','UserController@getFoto');
Route::get('/api/user/detail/{id}','UserController@detail');
Route::get('/api/user/all/{idrol?}','UserController@all');
Route::put('/api/user/updatePasswd', 'UserController@updatePasswd');
Route::get('/api/user/publico/{id}', 'UserController@infoPublica');
//fin rutas de usuario

//ruta empresas
Route::get('/api/empresas', 'UserController@empresas');
Route::get('/api/empresas/filtro', 'UserController@empresasBusqueda');
Route::get('/api/empresas/filtroAvanzado', 'UserController@empresasBusquedaAvanzada');
//fin ruta empresa

//rutas de publicaciones
Route::resource('/api/publicaciones', 'PublicarController');
Route::post('/api/publicaciones/tusPublicaciones', 'PublicarController@misPublicaciones');
Route::post('/api/publicaciones/publicacionesIndividuales', 'PublicarController@indexPersonal');
Route::get('/api/publicaciones/buscar/{email}', 'PublicarController@indexSearch');
Route::get('/api/publicaciones/perfil/{id}', 'PublicarController@PublicacionesId');
//fin de publicaciones

//rutas de ofertas de empleo 
Route::resource('/api/ofertas', 'OfertaController');
Route::get('/api/busqueda', 'OfertaController@ofertaBusqueda');
Route::get('/api/cantidadEmpleo', 'OfertaController@cantidadEmpleo');
Route::post('/api/ofertas/misOfertas', 'OfertaController@misOfertas');
Route::get('/api/busquedaAvanzada/{campo}', 'OfertaController@ofertaBusquedaAvanzada');
Route::get('/api/busquedaPerfil/{campo}', 'OfertaController@ofertaId');
Route::put('/api/finalizarOferta/{idusuario}/{idoferta}', 'OfertaController@finalizarOferta');
Route::get('/api/totalOfertas/{idusuario}', 'OfertaController@totalOfertaEmpresa');
Route::put('/api/ofertaUpdate', 'OfertaController@updateOferta');
//fin ofertas de empleo

//rutas inscripcion
Route::resource('/api/inscripcion', 'InscripcionController');
Route::post('/api/inscripcion/misInccripciones', 'InscripcionController@misInscripciones');
Route::post('/api/inscripcion/inscripcionesCandidatos', 'InscripcionController@CandidatosParaOfertaEmpresa');
Route::put('/api/descartarCandidato/{idusuario}/{idoferta}', 'InscripcionController@descartarCandidato');
Route::put('/api/seleccionarCandidato/{idusuario}/{idoferta}', 'InscripcionController@seleccionarCandidato');
Route::delete('/api/inscripcion/borrar/{idusuario}/{idoferta}', 'InscripcionController@destroyInscripcion');
Route::get('/api/inscripcion/verificar/{idusuario}/{idoferta}', 'InscripcionController@verificarInscripcion');
//fin rutas inscripcion

//rutas fav
Route::resource('/api/fav', 'FavController');
Route::post('/api/fav/misFav', 'FavController@misFav');
Route::delete('/api/fav/borrar/{idusuario}/{idoferta}', 'FavController@destroyFav');
Route::get('/api/fav/verificar/{idusuario}/{idoferta}', 'FavController@verificarFav');
//fin fav

//rutas comentario
Route::resource('/api/comentario', 'ComentarioController');
Route::post('/api/comentario/comentarioEnPublicacion', 'ComentarioController@comentariosDePublicaciones');
//fin comentario