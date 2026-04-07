// Import ajax
import { requestGET } from './ajax.js';

// Récupérer la textarea par son ID
const textarea = document.getElementById('content');
textarea.value = (await requestGET('/index.php?page=api_logs')).logs;