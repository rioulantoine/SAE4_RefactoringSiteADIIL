// Import ajax
import { requestGET } from './ajax.js';

// Récupérer la textarea par son ID
const textarea = document.getElementById('content');
textarea.value = (await requestGET('/logs.php')).logs;