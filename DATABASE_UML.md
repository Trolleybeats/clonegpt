# Diagramme UML - Base de Données CloneGPT

## 📊 Diagramme de Classes UML

```
┌─────────────────────────────────────────────────────────────┐
│                          USERS                              │
├─────────────────────────────────────────────────────────────┤
│ PK  id                      : BIGINT UNSIGNED               │
│     name                    : VARCHAR(255)                  │
│ UK  email                   : VARCHAR(255)                  │
│     email_verified_at       : TIMESTAMP NULL                │
│     password                : VARCHAR(255)                  │
│     preferred_model         : VARCHAR(255) NULL             │
│     instructions            : TEXT NULL                     │
│     two_factor_secret       : TEXT NULL                     │
│     two_factor_recovery_codes: TEXT NULL                    │
│     two_factor_confirmed_at : TIMESTAMP NULL                │
│     remember_token          : VARCHAR(100) NULL             │
│     created_at              : TIMESTAMP                     │
│     updated_at              : TIMESTAMP                     │
├─────────────────────────────────────────────────────────────┤
│ + conversations()           : HasMany<Conversation>         │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ 1
                              │
                              │ owns
                              │
                              │ *
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      CONVERSATIONS                          │
├─────────────────────────────────────────────────────────────┤
│ PK  id                      : BIGINT UNSIGNED               │
│ FK  user_id                 : BIGINT UNSIGNED               │
│     title                   : VARCHAR(255) NULL             │
│     model                   : VARCHAR(255) NULL             │
│     created_at              : TIMESTAMP                     │
│     updated_at              : TIMESTAMP                     │
├─────────────────────────────────────────────────────────────┤
│ + user()                    : BelongsTo<User>               │
│ + messages()                : HasMany<Message>              │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ 1
                              │
                              │ contains
                              │
                              │ *
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         MESSAGES                            │
├─────────────────────────────────────────────────────────────┤
│ PK  id                      : BIGINT UNSIGNED               │
│ FK  conversation_id         : BIGINT UNSIGNED               │
│     role                    : ENUM('user','assistant',      │
│                                    'system')                │
│     content                 : TEXT                          │
│     created_at              : TIMESTAMP                     │
│     updated_at              : TIMESTAMP                     │
├─────────────────────────────────────────────────────────────┤
│ + conversation()            : BelongsTo<Conversation>       │
└─────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────┐
│                PASSWORD_RESET_TOKENS                        │
├─────────────────────────────────────────────────────────────┤
│ PK  email                   : VARCHAR(255)                  │
│     token                   : VARCHAR(255)                  │
│     created_at              : TIMESTAMP NULL                │
└─────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────┐
│                         SESSIONS                            │
├─────────────────────────────────────────────────────────────┤
│ PK  id                      : VARCHAR(255)                  │
│ FK  user_id                 : BIGINT UNSIGNED NULL          │
│ IDX ip_address              : VARCHAR(45) NULL              │
│     user_agent              : TEXT NULL                     │
│     payload                 : LONGTEXT                      │
│ IDX last_activity           : INTEGER                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 Description des Tables

### 1. **USERS** (Utilisateurs)

Table centrale contenant les informations des utilisateurs de l'application.

| Colonne                     | Type            | Contraintes        | Description                                |
| --------------------------- | --------------- | ------------------ | ------------------------------------------ |
| `id`                        | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Identifiant unique de l'utilisateur        |
| `name`                      | VARCHAR(255)    | NOT NULL           | Nom complet de l'utilisateur               |
| `email`                     | VARCHAR(255)    | UNIQUE, NOT NULL   | Adresse email (identifiant de connexion)   |
| `email_verified_at`         | TIMESTAMP       | NULL               | Date de vérification de l'email            |
| `password`                  | VARCHAR(255)    | NOT NULL           | Mot de passe hashé (bcrypt)                |
| `preferred_model`           | VARCHAR(255)    | NULL               | Modèle IA préféré de l'utilisateur         |
| `instructions`              | TEXT            | NULL               | Instructions personnalisées pour l'IA      |
| `two_factor_secret`         | TEXT            | NULL               | Clé secrète pour l'authentification 2FA    |
| `two_factor_recovery_codes` | TEXT            | NULL               | Codes de récupération 2FA (JSON)           |
| `two_factor_confirmed_at`   | TIMESTAMP       | NULL               | Date de confirmation du 2FA                |
| `remember_token`            | VARCHAR(100)    | NULL               | Token pour la fonctionnalité "Se souvenir" |
| `created_at`                | TIMESTAMP       | NOT NULL           | Date de création du compte                 |
| `updated_at`                | TIMESTAMP       | NOT NULL           | Date de dernière modification              |

**Règles métier :**

- L'email doit être unique dans le système
- Le mot de passe est automatiquement hashé via Laravel
- Les instructions personnalisées permettent à l'utilisateur de définir le comportement de l'IA

---

### 2. **CONVERSATIONS** (Conversations)

Table stockant les conversations entre les utilisateurs et l'IA.

| Colonne      | Type            | Contraintes        | Description                                            |
| ------------ | --------------- | ------------------ | ------------------------------------------------------ |
| `id`         | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Identifiant unique de la conversation                  |
| `user_id`    | BIGINT UNSIGNED | FK, NOT NULL       | Référence vers l'utilisateur propriétaire              |
| `title`      | VARCHAR(255)    | NULL               | Titre de la conversation (auto-généré ou personnalisé) |
| `model`      | VARCHAR(255)    | NULL               | Modèle IA utilisé pour cette conversation              |
| `created_at` | TIMESTAMP       | NOT NULL           | Date de création de la conversation                    |
| `updated_at` | TIMESTAMP       | NOT NULL           | Date de dernière activité                              |

**Règles métier :**

- Chaque conversation appartient à un seul utilisateur
- Le titre peut être généré automatiquement à partir du premier message
- Le modèle peut être différent du modèle préféré de l'utilisateur

---

### 3. **MESSAGES** (Messages)

Table contenant tous les messages échangés dans les conversations.

| Colonne           | Type            | Contraintes        | Description                                        |
| ----------------- | --------------- | ------------------ | -------------------------------------------------- |
| `id`              | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Identifiant unique du message                      |
| `conversation_id` | BIGINT UNSIGNED | FK, NOT NULL       | Référence vers la conversation parente             |
| `role`            | ENUM            | NOT NULL           | Rôle de l'émetteur : 'user', 'assistant', 'system' |
| `content`         | TEXT            | NOT NULL           | Contenu textuel du message                         |
| `created_at`      | TIMESTAMP       | NOT NULL           | Date d'envoi du message                            |
| `updated_at`      | TIMESTAMP       | NOT NULL           | Date de modification                               |

**Valeurs possibles pour `role` :**

- `user` : Message envoyé par l'utilisateur
- `assistant` : Réponse générée par l'IA
- `system` : Message système (instructions, contexte)

**Règles métier :**

- Les messages sont ordonnés chronologiquement via `created_at`
- Le rôle détermine la présentation visuelle et le traitement
- Le contenu peut contenir du Markdown

---

### 4. **PASSWORD_RESET_TOKENS** (Tokens de réinitialisation)

Table système pour la gestion des réinitialisations de mots de passe.

| Colonne      | Type         | Contraintes  | Description                     |
| ------------ | ------------ | ------------ | ------------------------------- |
| `email`      | VARCHAR(255) | PK, NOT NULL | Email de l'utilisateur          |
| `token`      | VARCHAR(255) | NOT NULL     | Token de réinitialisation hashé |
| `created_at` | TIMESTAMP    | NULL         | Date de création du token       |

**Règles métier :**

- Les tokens expirent après un délai configurable (typiquement 60 minutes)
- Un email ne peut avoir qu'un seul token actif à la fois
- Le token est invalidé après utilisation

---

### 5. **SESSIONS** (Sessions)

Table système pour la gestion des sessions utilisateurs.

| Colonne         | Type            | Contraintes     | Description                                            |
| --------------- | --------------- | --------------- | ------------------------------------------------------ |
| `id`            | VARCHAR(255)    | PK              | Identifiant unique de session                          |
| `user_id`       | BIGINT UNSIGNED | FK, NULL, INDEX | Référence vers l'utilisateur (NULL si non authentifié) |
| `ip_address`    | VARCHAR(45)     | NULL            | Adresse IP du client (IPv4/IPv6)                       |
| `user_agent`    | TEXT            | NULL            | User-Agent du navigateur                               |
| `payload`       | LONGTEXT        | NOT NULL        | Données de session sérialisées                         |
| `last_activity` | INTEGER         | INDEX, NOT NULL | Timestamp UNIX de dernière activité                    |

**Règles métier :**

- Les sessions expirées sont nettoyées automatiquement
- L'index sur `last_activity` optimise le nettoyage
- Supporte les sessions anonymes (`user_id` NULL)

---

## 🔗 Relations et Cardinalités

### Relation 1 : USER → CONVERSATIONS

- **Type** : One-to-Many (1:N)
- **Clé étrangère** : `conversations.user_id` → `users.id`
- **Cascade** : `ON DELETE CASCADE`
- **Description** : Un utilisateur peut créer plusieurs conversations. Si l'utilisateur est supprimé, toutes ses conversations sont supprimées.

```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
```

---

### Relation 2 : CONVERSATION → MESSAGES

- **Type** : One-to-Many (1:N)
- **Clé étrangère** : `messages.conversation_id` → `conversations.id`
- **Cascade** : `ON DELETE CASCADE`
- **Description** : Une conversation contient plusieurs messages. Si la conversation est supprimée, tous ses messages sont supprimés.

```sql
FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
```

---

### Relation 3 : USER → SESSIONS

- **Type** : One-to-Many (1:N)
- **Clé étrangère** : `sessions.user_id` → `users.id`
- **Cascade** : Aucune (gestion manuelle)
- **Description** : Un utilisateur peut avoir plusieurs sessions actives. Relation optionnelle (sessions anonymes possibles).

---

## 🔒 Contraintes et Règles d'Intégrité

### Contraintes de Clé Primaire (PK)

| Table           | Colonne | Type           |
| --------------- | ------- | -------------- |
| `users`         | `id`    | AUTO_INCREMENT |
| `conversations` | `id`    | AUTO_INCREMENT |
| `messages`      | `id`    | AUTO_INCREMENT |

### Contraintes d'Unicité (UNIQUE)

| Table   | Colonne | Raison                      |
| ------- | ------- | --------------------------- |
| `users` | `email` | Un email = un compte unique |

### Contraintes de Clé Étrangère (FK)

| Table Source    | Colonne           | Table Cible     | Colonne | Action         |
| --------------- | ----------------- | --------------- | ------- | -------------- |
| `conversations` | `user_id`         | `users`         | `id`    | CASCADE DELETE |
| `messages`      | `conversation_id` | `conversations` | `id`    | CASCADE DELETE |

### Contraintes CHECK

```sql
-- Implicite via ENUM dans messages.role
CHECK (role IN ('user', 'assistant', 'system'))
```

### Contraintes NOT NULL

- **users** : `name`, `email`, `password`
- **conversations** : `user_id`
- **messages** : `conversation_id`, `role`, `content`
- **sessions** : `payload`, `last_activity`

### Index de Performance

```sql
-- Index sur les clés étrangères
INDEX idx_conversations_user_id ON conversations(user_id)
INDEX idx_messages_conversation_id ON messages(conversation_id)
INDEX idx_sessions_user_id ON sessions(user_id)

-- Index sur les colonnes fréquemment recherchées
INDEX idx_sessions_last_activity ON sessions(last_activity)
```

---

## 📐 Diagramme Entité-Association (ERD)

```
USERS (1) ────────< (N) CONVERSATIONS (1) ────────< (N) MESSAGES
  │
  │ (identifie)
  └──────────< (N) SESSIONS (optionnel)
```

**Légende :**

- `(1)` : Cardinalité "Un"
- `(N)` : Cardinalité "Plusieurs"
- `────<` : Relation "a plusieurs"
- `ON DELETE CASCADE` : Suppression en cascade

---

## 🛡️ Règles d'Intégrité Référentielle

### 1. Intégrité de Domaine

- Les emails sont validés par Laravel (format RFC)
- Les mots de passe doivent respecter les règles de sécurité (min 8 caractères)
- Les rôles de messages sont strictement contrôlés par ENUM
- Les timestamps sont gérés automatiquement par Laravel

### 2. Intégrité d'Entité

- Toutes les tables principales ont une clé primaire auto-incrémentée
- Pas de doublons autorisés sur les colonnes UNIQUE
- Les valeurs NULL sont explicitement définies

### 3. Intégrité Référentielle

- **CASCADE DELETE** : La suppression d'un utilisateur supprime ses conversations et tous les messages associés
- **RESTRICT** : Impossible de supprimer un utilisateur avec des sessions actives (gestion applicative)
- **SET NULL** : Non utilisé dans ce schéma

### 4. Intégrité Sémantique

- Un message doit toujours appartenir à une conversation existante
- Une conversation doit toujours appartenir à un utilisateur existant
- Les dates `created_at` et `updated_at` sont gérées automatiquement
- Le rôle 'system' est réservé aux messages d'initialisation

---

## 🔄 Flux de Données Typiques

### Création d'une nouvelle conversation :

1. Utilisateur authentifié (vérification dans `users`)
2. Création d'un enregistrement dans `conversations`
3. Insertion du premier message dans `messages` (role='user')
4. Génération de la réponse IA (role='assistant')
5. Mise à jour de `conversations.updated_at`

### Suppression en cascade :

```
DELETE users WHERE id = 1
  ↓ (CASCADE)
DELETE conversations WHERE user_id = 1
  ↓ (CASCADE)
DELETE messages WHERE conversation_id IN (conversations_ids)
```

---

## 📊 Métriques et Optimisations

### Index Recommandés

```sql
-- Recherche de conversations par utilisateur
CREATE INDEX idx_conversations_user_created
ON conversations(user_id, created_at DESC);

-- Tri des messages par conversation
CREATE INDEX idx_messages_conversation_created
ON messages(conversation_id, created_at ASC);

-- Nettoyage des sessions expirées
CREATE INDEX idx_sessions_last_activity
ON sessions(last_activity);
```

### Requêtes Optimisées

```sql
-- Récupérer les conversations récentes avec comptage de messages
SELECT c.*, COUNT(m.id) as message_count
FROM conversations c
LEFT JOIN messages m ON c.id = m.conversation_id
WHERE c.user_id = ?
GROUP BY c.id
ORDER BY c.updated_at DESC
LIMIT 20;
```

---

## 📝 Notes de Conformité UML

Ce diagramme respecte les standards UML 2.5 :

- ✅ Notation des classes avec compartiments (attributs, méthodes)
- ✅ Types de données explicites
- ✅ Contraintes d'intégrité documentées
- ✅ Cardinalités précises (1:N)
- ✅ Clés primaires (PK) et étrangères (FK) identifiées
- ✅ Relations d'association avec noms de rôles
- ✅ Contraintes CHECK et UNIQUE documentées
- ✅ Actions référentielles (CASCADE) spécifiées

---

## 🎯 Bonnes Pratiques Appliquées

1. **Normalisation** : Base en 3ème forme normale (3NF)
2. **Sécurité** : Mots de passe hashés, tokens 2FA
3. **Performance** : Index sur clés étrangères et colonnes recherchées
4. **Maintenabilité** : Timestamps automatiques, soft deletes possibles
5. **Évolutivité** : Structure modulaire, ajout de colonnes possible
6. **Intégrité** : Cascades appropriées, contraintes strictes

---

## 🏗️ Architecture Logicielle

### 3.2.1 Organisation du Code Laravel

#### Structure MVC

```
app/
├── Http/
│   ├── Controllers/          # Contrôleurs
│   │   ├── AskController.php           # Gestion simple sans streaming
│   │   ├── AskStreamController.php     # Streaming SSE temps réel
│   │   ├── ConversationController.php  # CRUD conversations
│   │   ├── MessageController.php       # Gestion des messages
│   │   ├── InstructionController.php   # Instructions personnalisées
│   │   └── Settings/                   # Paramètres utilisateur
│   ├── Middleware/           # Middleware personnalisés
│   └── Requests/             # Form Requests (validation)
│
├── Models/                   # Modèles Eloquent
│   ├── User.php             # Utilisateur avec 2FA
│   ├── Conversation.php     # Conversation avec messages
│   └── Message.php          # Message (user/assistant/system)
│
├── Policies/                # Politiques d'autorisation
│   └── ConversationPolicy.php  # Contrôle d'accès conversations
│
├── Services/                # Couche service (logique métier)
│   ├── SimpleAskService.php       # Communication API OpenRouter
│   └── SimpleAskStreamService.php # Streaming SSE OpenRouter
│
├── Providers/               # Service Providers
│   ├── AppServiceProvider.php    # Enregistrement des policies
│   └── FortifyServiceProvider.php # Configuration authentification
│
└── Actions/                 # Actions Fortify
    └── Fortify/
        └── ...             # Actions auth personnalisées
```

#### Patterns et Principes

**1. Repository Pattern (via Services)**

```php
// Services encapsulent la logique métier et API externes
class SimpleAskService {
    public function getModels(): array { /* ... */ }
    public function sendMessage(array $messages, string $model): array { /* ... */ }
}
```

**2. Policy-Based Authorization**

```php
// ConversationPolicy contrôle l'accès
Gate::policy(Conversation::class, ConversationPolicy::class);

// Utilisation dans les contrôleurs
$this->authorize('view', $conversation);
```

**3. Dependency Injection**

```php
// Injection automatique via constructeur
public function __construct(
    private SimpleAskStreamService $streamService
) {}
```

**4. Form Request Validation**

```php
$validated = $request->validate([
    'message' => 'required|string|max:100000',
    'model' => 'required|string',
    'temperature' => 'nullable|numeric|min:0|max:2',
]);
```

**5. Eloquent ORM avec Relations**

```php
// User.php
public function conversations(): HasMany {
    return $this->hasMany(Conversation::class);
}

// Conversation.php
public function messages(): HasMany {
    return $this->hasMany(Message::class);
}
public function user(): BelongsTo {
    return $this->belongsTo(User::class);
}
```

---

### 3.2.2 Structure des Composants Vue.js

#### Architecture Frontend

```
resources/js/
├── app.ts                    # Point d'entrée Inertia.js
├── ssr.ts                    # Server-Side Rendering
│
├── components/               # Composants réutilisables
│   ├── ui/                  # Composants UI primitifs (shadcn/ui)
│   │   ├── button/
│   │   ├── card/
│   │   ├── dialog/
│   │   ├── input/
│   │   ├── sidebar/
│   │   └── ...
│   │
│   ├── AppShell.vue         # Shell principal (sidebar provider)
│   ├── AppSidebar.vue       # Barre latérale navigation
│   ├── AppHeader.vue        # En-tête application
│   ├── Message.vue          # Affichage message (user/assistant)
│   ├── NavMain.vue          # Navigation principale
│   ├── NavUser.vue          # Menu utilisateur
│   ├── TwoFactorSetupModal.vue  # Configuration 2FA
│   └── ...
│
├── layouts/                 # Layouts principaux
│   ├── AppLayout.vue        # Layout avec sidebar
│   ├── AuthLayout.vue       # Layout authentification
│   └── app/
│       └── AppSidebarLayout.vue
│
├── pages/                   # Pages Inertia
│   ├── Welcome.vue          # Page d'accueil
│   ├── Dashboard.vue        # Tableau de bord
│   ├── conversations/
│   │   ├── index.vue        # Liste conversations
│   │   ├── create.vue       # Nouvelle conversation
│   │   └── show.vue         # Conversation avec streaming
│   ├── settings/            # Pages paramètres
│   │   ├── profile.vue
│   │   ├── appearance.vue
│   │   └── security.vue
│   └── auth/                # Pages authentification
│       ├── login.vue
│       ├── register.vue
│       └── ...
│
├── composables/             # Composition API hooks
│   ├── useStream.ts         # Hook streaming SSE
│   ├── useAppearance.ts     # Gestion thème (light/dark)
│   ├── useTwoFactorAuth.ts  # Gestion 2FA
│   └── useInitials.ts       # Génération initiales avatar
│
│
└── routes/                  # Définitions routes Wayfinder
    └── ...
```

#### Patterns Vue.js Utilisés

**1. Composition API**

```typescript
// useStream.ts - Composable réutilisable
export function useStream(endpoint: string, options = {}) {
    const data = ref<string>('');
    const isStreaming = ref<boolean>(false);

    async function send(payload?: unknown): Promise<void> {
        // Logique streaming SSE
    }

    return { data, isStreaming, send };
}
```

**2. Props avec TypeScript**

```vue
<script setup lang="ts">
interface Props {
    conversation: Conversation;
    models: Model[];
    selectedModel: string;
}

const props = defineProps<Props>();
</script>
```

**3. Inertia.js pour SPA**

```vue
<!-- Navigation sans rechargement complet -->
<Link :href="route('conversations.show', conversation.id)">
    {{ conversation.title }}
</Link>
```

**4. Composants UI Modulaires (shadcn/ui)**

```vue
<template>
    <Card>
        <CardHeader>
            <CardTitle>Titre</CardTitle>
        </CardHeader>
        <CardContent>
            <!-- Contenu -->
        </CardContent>
    </Card>
</template>
```

**5. Reactive State Management**

```typescript
// Gestion du thème avec localStorage
const appearance = ref<'light' | 'dark' | 'system'>('system');

watch(appearance, (value) => {
    localStorage.setItem('appearance', value);
    applyTheme(value);
});
```

---

### 3.2.3 Services et Patterns Utilisés

#### Services Backend

**SimpleAskService** - Communication API sans streaming

- Récupération liste des modèles (avec cache 1h)
- Envoi de messages simples (requête/réponse)
- Gestion erreurs et logging
- Pattern : Service Layer + HTTP Client (Laravel)

**SimpleAskStreamService** - Streaming SSE temps réel

- Streaming SSE (Server-Sent Events)
- Generator PHP pour flux continu
- Support paramètres avancés (temperature, reasoning_effort)
- Pattern : Service Layer + Generator + StreamedResponse

#### Patterns d'Architecture

**1. Service Layer Pattern**

```php
// Encapsulation logique métier dans services
class SimpleAskStreamService {
    private string $apiKey;
    private string $baseUrl;

    public function streamMessage(array $messages): Generator {
        // Logique streaming isolée
    }
}
```

**2. Dependency Injection Container**

```php
// Résolution automatique des dépendances
public function __construct(
    private SimpleAskService $askService
) {}
```

**3. Policy-Based Authorization**

```php
// Séparation des préoccupations d'autorisation
class ConversationPolicy {
    public function view(User $user, Conversation $conversation): bool {
        return $conversation->user_id === $user->id;
    }
}
```

**4. Repository Pattern (Eloquent)**

```php
// ORM comme couche d'abstraction données
$conversations = Conversation::where('user_id', auth()->id())
    ->with('messages')
    ->orderBy('updated_at', 'desc')
    ->get();
```

**5. Streaming avec Generators**

```php
// Utilisation de generators pour streaming mémoire efficace
public function streamMessage(): Generator {
    foreach ($this->readStream() as $chunk) {
        yield "data: $chunk\n\n";
    }
}
```

**6. Frontend Composables Pattern**

```typescript
// Logique réutilisable avec Composition API
export function useStream(endpoint: string) {
    // État réactif
    // Logique métier
    // API exposée
    return { data, isStreaming, send, cancel };
}
```

**7. Server-Sent Events (SSE)**

```typescript
// Communication temps réel unidirectionnelle
const reader = response.body.getReader();
while (true) {
    const { done, value } = await reader.read();
    if (done) break;
    // Traitement chunk par chunk
}
```

**8. Middleware Pipeline**

```php
// Routes protégées par middleware
Route::middleware('auth')->group(function() {
    Route::resource('conversations', ConversationController::class);
});
```

#### Technologies Clés

- **Backend** : Laravel 11, PHP 8.2+, Eloquent ORM
- **Frontend** : Vue 3 (Composition API), TypeScript, Inertia.js
- **UI** : Tailwind CSS, shadcn/ui (Radix Vue)
- **Auth** : Laravel Fortify (2FA inclus)
- **API** : OpenRouter (streaming SSE)
- **Cache** : Redis/File (modèles API)
- **Build** : Vite

---

**Date de création** : 29 décembre 2025  
**Version** : 1.0  
**Framework** : Laravel 11  
**Base de données** : MySQL 8.0+ / MariaDB 10.3+
