<?php

namespace App\Neuron\ChatMemory;

use App\Models\Conversation;
use App\Models\Message;
use NeuronAI\Chat\History\AbstractChatHistory;
use NeuronAI\Chat\Messages\Message as ChatMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\AssistantMessage;

class DatabaseChatHistory extends AbstractChatHistory
{
    protected Conversation $conversation;

    public function __construct(Conversation $conversation, int $contextWindow = 90000)
    {
        $this->conversation = $conversation;
        $this->contextWindow = $contextWindow;
        $this->loadMessages();
       // $this->removeOldestMessage();
    }


    protected function storeMessage(ChatMessage $message): static
    {
        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender' => $message->getRole(),
            'message' => $message->getContent()?? 'tool call',
            // 'user_id' => $this->conversation->user_id, // Descomenta si lo necesitas
        ]);

        return $this;
    }

    public function removeOldestMessage(): static
    {
        $this->conversation->messages()->oldest()->first()?->delete();
        return $this;
    }

    protected function clear(): static
    {
        $this->conversation->messages()->delete();
        return $this;
    }

    protected function loadMessages(): void
    {
        foreach ($this->conversation->messages()->orderBy('created_at')->get() as $msg) {
            // Determina el tipo de mensaje basado en el campo 'sender'
            $message = match ($msg->sender) {
                ChatMessage::ROLE_USER => new UserMessage($msg->message),
                ChatMessage::ROLE_ASSISTANT => new AssistantMessage($msg->message),
                default => new ChatMessage($msg->sender, $msg->message)
            };

            $this->history[] = $message;
        }
    }

protected function loadMessages2(): void
{
    $messages = $this->conversation->messages()
        ->orderBy('created_at', 'desc')
        ->limit(5) // 👈 solo los últimos 5
        ->get()
        ->reverse(); // 👈 para restaurar el orden cronológico

    foreach ($messages as $msg) {
        $message = match ($msg->sender) {
            ChatMessage::ROLE_USER => new UserMessage($msg->message),
            ChatMessage::ROLE_ASSISTANT => new AssistantMessage($msg->message),
            default => new ChatMessage($msg->sender, $msg->message)
        };

        $this->addMessage($message);
    }
}


/*
protected function loadMessages(): void
{
    foreach ($this->conversation->messages()->orderBy('created_at')->get() as $msg) {
        $role = $msg->sender;

        $message = match ($role) {
            'user' => new UserMessage($msg->message),
            'assistant' => new AssistantMessage($msg->message),
            default => new ChatMessage($role, $msg->message),
        };

        $this->addMessage($message);
    }
} */

}
