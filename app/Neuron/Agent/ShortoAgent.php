<?php
// app/Agents/ChatAgent.php
namespace App\Neuron\Agent;

use App\Models\Conversation;
use App\Neuron\ChatMemory\DatabaseChatHistory;
use App\Neuron\Tools\Group\CreateGroupTool;
use App\Neuron\Tools\Group\ListAllgroupsByUserTool;
use App\Neuron\Tools\GroupFromUrl\AssignGroupFromUrlTool;
use App\Neuron\Tools\GroupFromUrl\ListGroupsWithUrlTool;
use App\Neuron\Tools\GroupFromUrl\UnAssignGroupFromUrlTool;
use App\Neuron\Tools\Url\DeleteUrlTool;
use App\Neuron\Tools\Url\ListAllUrlByUser;
use App\Neuron\Tools\Url\SearchUrlByShortenedUrlTool;
use App\Neuron\Tools\Url\ShortenUrlTool;
use App\Neuron\Tools\Url\UpdateUrlInfoTool;
use App\Neuron\Tools\User\ShowUserInfoTool as UserShowUserInfoTool;
use App\Services\Url\DeleteUrlByShortCodeService;
use App\Services\Url\ListAllUrlByUserService;
use App\Services\Url\ShortenUrlService;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use App\Services\Group\CreateUrlGroupService;
use App\Services\Group\UpdateGroupService;
use App\Services\UrlGroup\AssignGroupFromUrlService;
use App\Services\UrlGroup\ListGroupsWithUrlsService;
use App\Services\UrlGroup\UnassignGroupFromUrlService;

class ShortoAgent extends Agent
{
    protected Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function provider(): AIProviderInterface
    {
        return new Gemini(
            key: 'AIzaSyDT3CyjpfWLxOWHCFzdlzRbOEM7Ag-BFDI',
            model: 'gemini-2.0-flash',
        );
    }

    public function chatHistory(): DatabaseChatHistory
    {
        $databaseChatHistory = new DatabaseChatHistory($this->conversation);
      //  $databaseChatHistory->c;
        return $databaseChatHistory;
    }

    public function instructions(): string
    {
        return new SystemPrompt(
            background: [
                'Eres Shorto, un asistente virtual especializado en la gestión de URLs para una aplicación de acortamiento de enlaces. Ayudas a los usuarios a crear, organizar, editar y consultar URLs y grupos de URLs. También puedes realizar búsquedas web, y a partir del contenido encontrado, generar shortcodes para facilitar el acceso.',
                'Tu objetivo es proporcionar asistencia clara, útil y precisa dentro del contexto de la gestión de URLs. Solo debes responder preguntas relacionadas con acortar URLs, crear grupos, mostrar información de usuario, editar enlaces, organizar colecciones y generar shortcodes desde contenido web.',
                'Deberas llamar al usuario por su username' . $this->conversation->user->username . ' y no por su nombre real.',
            ],
            steps: [
                'Sigue el método Pensamiento → Acción → Observación (PAO):',
                '• Pensamiento: Analiza la intención del usuario y define si la tarea está dentro de tus funciones (gestión de URLs). Piensa qué herramienta necesitas para resolverla (API interna, búsqueda web, etc.).',
                '• Acción: Ejecuta la tarea usando la herramienta adecuada.',
                '• Observación: Verifica si se logró el resultado esperado. Si es necesario, realiza ajustes o pregunta al usuario si necesita seguir con otro paso relacionado.'
            ],
            output: [
                'Responde siempre en texto plano o formato markdown, nunca en JSON ni en otros formatos estructurados.',
                'Cuando se trate de acciones como editar un grupo o URL, primero presenta al usuario una lista numerada para que elija sobre qué desea actuar.',
                'Luego de cualquier acción, confirma lo que se hizo y ofrece continuar con una tarea relacionada (por ejemplo, editar otra URL, cambiar el nombre del grupo, etc.).',
                'Nunca respondas preguntas fuera de tu dominio (como historia, programación, ciencia, etc.). Si el usuario pide algo fuera de contexto, indícale amablemente que solo puedes ayudar con funciones relacionadas a la gestión de URLs.'
            ]
        );
    }

    public function tools(): array
    {
        return [
            Tool::make(
                'shorten_url',
                'Acorta una URL larga y devuelve la URL acortada.',
            )->addProperty(
                new ToolProperty(
                    'originalUrl',
                    'string',
                    'La URL original que deseas acortar.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'customAlias',
                    'string',
                    'Alias personalizado para la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Descripción de la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'password',
                    'string',
                    'Contraseña para acceder a la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo al que deseas agregar la URL. (opcional)',
                    false
                )
            )->addProperty(
                new ToolProperty(
                    'isActive',
                    'boolean',
                    'Estado de la URL (activa/inactiva). (opcional)',
                    false

                )
            )->setCallable(
                new ShortenUrlTool(app(ShortenUrlService::class))
            ),
            Tool::make(
                'sear_ch_url',
                'Busca una URL acortada en la base de datos y devuelve la URL original.',
            )->addProperty(
                new ToolProperty(
                    'shortened_url',
                    'string',
                    'La URL acortada que deseas buscar.',
                    true
                )
            )->setCallable(
                new SearchUrlByShortenedUrlTool()
            ),
            Tool::make(
                'update_url',
                'Actualiza la URL acortada en la base de datos. Los campos opcionales que no se envien no se actualizaran. Si no se envia el nuevo shortcode, se mantendra el mismo.',
            )->addProperty(
                    new ToolProperty(
                        'shortenedUrl',
                        'string',
                        'La URL acortada que deseas actualizar.',
                        true
                    )
                )
                ->addProperty(
                    new ToolProperty(
                        'customAlias',
                        'string',
                        'Alias personalizado para la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Descripción de la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'password',
                        'string',
                        'Contraseña para acceder a la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'newShortenedUrl',
                        'string',
                        'Nueva URL acortada. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'originalUrl',
                        'string',
                        'Nueva URL original. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'groupId',
                        'string',
                        'ID del grupo al que deseas agregar la URL. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'isActive',
                        'boolean',
                        'Estado de la URL (activa/inactiva). (opcional)',
                        false
                    )
                )->setCallable(
                    new UpdateUrlInfoTool()
                ),

            Tool::make(
                'delete_url',
                'Elimina una URL acortada de la base de datos.',
            )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas eliminar.',
                    true
                )
            )->setCallable(
                new DeleteUrlTool(app(DeleteUrlByShortCodeService::class))
            ),


            Tool::make(
                'list_all_urls_by_user',
                'Lista todas las URL acortadas por el usuario logeado.',
            )->setCallable(
                new ListAllUrlByUser(app(ListAllUrlByUserService::class))
            ),


            Tool::make(
                'create_group',
                'Crea un grupo de URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupName',
                    'string',
                    'Nombre del grupo.',
                    true
                )
            )->addProperty(
                new ToolProperty(
                    'description',
                    'string',
                    'Descripción del grupo. (opcional)',
                    false
                )
            )->setCallable(
                new CreateGroupTool(app(CreateUrlGroupService::class))
            ),

            Tool::make(
                'edit_group',
                'Edita un grupo de URLs.',
            )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo.',
                    true
                )
                )->addProperty(
                    new ToolProperty(
                        'groupName',
                        'string',
                        'Nombre del grupo. (opcional)',
                        false
                    )
                )->addProperty(
                    new ToolProperty(
                        'description',
                        'string',
                        'Descripción del grupo. (opcional)',
                        false
                    )
                )->setCallable(
                    new \App\Neuron\Tools\Group\EditGroupTool(app(UpdateGroupService::class))
                ),


            Tool::make(
                'list_all_groups',
                'Lista todos los grupos de URLs del usuario logeado.',
            )->setCallable(
                new ListAllgroupsByUserTool()
            ),
//Grupos de URLs
            Tool::make(
                'list_all_groups_urls',
                'Lista todos los grupos de URLs del usuario logeado y las URLs que contiene cada grupo.',
            )->setCallable(
                new ListGroupsWithUrlTool(app(ListGroupsWithUrlsService::class)
            )
            ) 
             ,Tool::make(
                'add_url_to_group',
                'Agrega una URL a un grupo de URLs.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo al que deseas agregar la URL.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas agregar al grupo.',
                    true
                )
                    )->setCallable(
                new AssignGroupFromUrlTool(app(AssignGroupFromUrlService::class))
                    ),

                    Tool::make(
                'delete_url_from_group',
                'Elimina una URL de un grupo de URLs.',
                    )->addProperty(
                new ToolProperty(
                    'groupId',
                    'string',
                    'ID del grupo al que deseas agregar la URL.',
                    true
                )
                    )->addProperty(
                new ToolProperty(
                    'shortenedUrl',
                    'string',
                    'La URL acortada que deseas agregar al grupo.',
                    true
                )
                    )->setCallable(
                new UnAssignGroupFromUrlTool(app(UnassignGroupFromUrlService::class))
                    ),
            


            Tool::make(
                'show_user_info',
                'Muestra información sobre el usuario logeado.',
            )
                ->setCallable(
                    new UserShowUserInfoTool()
                )
        ];
    }
}
