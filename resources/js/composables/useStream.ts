import { ref, type Ref } from 'vue';

type UseStreamOptions = {
    headers?: Record<string, string>;
    onFinish?: () => void;
    onError?: (err: Error) => void;
};

type UseStreamReturn = {
    data: Ref<string>;
    isFetching: Ref<boolean>;
    isStreaming: Ref<boolean>;
    send: (payload?: unknown) => Promise<void>;
    cancel: () => void;
};

function getCsrfToken(): string | null {
    const el = document.querySelector(
        'meta[name="csrf-token"]',
    ) as HTMLMetaElement | null;
    return el?.content ?? null;
}

export function useStream(
    endpoint: string,
    options: UseStreamOptions = {},
): UseStreamReturn {
    const data = ref<string>('');
    const isFetching = ref<boolean>(false);
    const isStreaming = ref<boolean>(false);
    let controller: AbortController | null = null;

    async function send(payload?: unknown): Promise<void> {
        if (isStreaming.value) return;

        data.value = '';
        isFetching.value = true;
        isStreaming.value = false;

        controller = new AbortController();

        try {
            const csrf = getCsrfToken();

            const res = await fetch(endpoint, {
                method: 'POST',
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                    ...(options.headers ?? {}),
                },
                credentials: 'same-origin',
                body: payload ? JSON.stringify(payload) : undefined,
            });

            isFetching.value = false;

            if (!res.ok || !res.body) {
                throw new Error(
                    `Stream request failed: ${res.status} ${res.statusText}`,
                );
            }

            isStreaming.value = true;
            const reader = res.body.getReader();
            const decoder = new TextDecoder();

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                if (value) {
                    const chunk = decoder.decode(value, { stream: true });
                    if (chunk) {
                        data.value += chunk;
                    }
                }
            }
        } catch (err: any) {
            // AbortError: user canceled
            if (err?.name !== 'AbortError') {
                options.onError?.(
                    err instanceof Error ? err : new Error(String(err)),
                );
            }
        } finally {
            isStreaming.value = false;
            isFetching.value = false;
            options.onFinish?.();
        }
    }

    function cancel(): void {
        if (controller) {
            controller.abort();
            controller = null;
        }
    }

    return { data, isFetching, isStreaming, send, cancel };
}
