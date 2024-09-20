<?php

// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Hash;
// use App\Services\FirebaseService;
// use Illuminate\Http\Request;

// class FirebaseTestController extends Controller
// {
//     protected $firebaseService;

//     public function __construct(FirebaseServices $firebaseService)
//     {
//         $this->firebaseService = $firebaseService;
//     }

//     // Test pour récupérer une collection Firebase
//     public function getCollection()
//     {
//         try {
//             $collectionName = 'users'; // Exemple de nom de collection
//             $data = $this->firebaseService->getCollection($collectionName)->getValue();
//             return response()->json($data, 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // Test pour récupérer un document spécifique dans Firebase
//     public function getDocument($documentId)
//     {
//         try {
//             $collectionName = 'users'; // Exemple de nom de collection
//             $document = $this->firebaseService->getDocument($collectionName, $documentId);
//             return response()->json($document, 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // Test pour insérer un document dans Firebase
//     public function saveDocument(Request $request)
//     {
//         try {
//             $collectionName = 'users'; // Exemple de nom de collection
//             $documentId = $request->input('id'); // ID du document            
//             $data = $request->only(['nom', 'prenom', 'photo', 'login',
//                                     'password', 'fonction', 'statut', 'role_id']); // Données à enregistrer
//             $data['password'] = Hash::make($request->input('password'));
//             $this->firebaseService->saveDocument($collectionName, $documentId, $data);
//             return response()->json(['message' => 'Document saved successfully!'], 201);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }
// }
