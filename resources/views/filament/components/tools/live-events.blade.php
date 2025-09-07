<div>
    @script
        <script>
            // Listen for custom user events
            window.addEventListener('DOMContentLoaded', async (event) => {
                let eventHash = @this.event_hash;
                let userId = @this.user_id;
                console.log('eventHash', eventHash);
                console.log('userId', userId);
                
                // Always connect to public channel first
                if(eventHash){
                    try {
                        const channel = Echo.channel('custom-event.' + eventHash);
                        channel.listen('CustomUserEvent', processEvent);
                        console.log('Connected to public channel:', 'custom-event.' + eventHash);
                    } catch(e) {
                        console.log('Error connecting to public channel:', e);
                    }
                }
                
                // Only connect to private channel if user is authenticated
                if(userId && userId > 0){
                    // Add a small delay to ensure Echo is fully initialized
                    setTimeout(() => {
                        try{
                            const channel = Echo.private('user.' + userId);
                            channel.listen('CustomUserEvent', processEvent)
                                .error((error) => {
                                    console.log('Private channel error:', error);
                                });
                            console.log('Connected to private channel:', 'user.' + userId);
                        }catch(e){
                            console.log('Error listening to private channel:', e);
                        }
                    }, 100);
                } else {
                    console.log('User not authenticated, skipping private channel');
                }
            });

            function processEvent(event){
                console.log('processEvent', event);
                if(event.type === 'dispatch'){
                    @this.dispatch(event.title, event.value);
                }else if(event.type === 'notify'){
                    @this.notify(event.title, event.value);
                }else if(event.type === 'redirect'){
                    window.location.href = event.value;
                }else if(event.type === 'set'){
                    @this.set(event.title, event.value);
                }else if(event.type === 'function'){
                    if(event.value){
                        if (event.value === "multi_args" && event.options && Array.isArray(event.options)) {
                            @this[event.title](...event.options);
                        } else {
                            @this[event.title](event.value);
                        }
                    }else{
                        @this[event.title]();
                    }
                }
            }
        </script>
    @endscript
</div>
