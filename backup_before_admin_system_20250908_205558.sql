--
-- PostgreSQL database dump
--

\restrict G9rVtFzJgEnkTpsGSyDW1lMfDqO0znZDiHMTgPdfWaK6ldsFtkjnbiiQ9XPkZVh

-- Dumped from database version 17.6 (Ubuntu 17.6-1.pgdg22.04+1)
-- Dumped by pg_dump version 17.6 (Ubuntu 17.6-1.pgdg22.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: ai_content_templates; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ai_content_templates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    prompt_template text NOT NULL,
    parameters json DEFAULT '{}'::json NOT NULL,
    category character varying(100),
    is_active boolean DEFAULT true NOT NULL,
    usage_count integer DEFAULT 0 NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ai_content_templates OWNER TO ali_master;

--
-- Name: ai_content_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ai_content_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ai_content_templates_id_seq OWNER TO ali_master;

--
-- Name: ai_content_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ai_content_templates_id_seq OWNED BY public.ai_content_templates.id;


--
-- Name: ai_contents; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ai_contents (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    short_description text NOT NULL,
    language character varying(255) DEFAULT 'English'::character varying NOT NULL,
    model_type character varying(255) DEFAULT 'fast'::character varying NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    ai_headings json,
    ai_sections json,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    og_title character varying(255),
    og_description text,
    twitter_title character varying(255),
    twitter_description text,
    schema json,
    json_ld json,
    faq json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    ai_summary text,
    ai_thumbnails json,
    model_id bigint,
    generation_settings json,
    generation_progress integer DEFAULT 0 NOT NULL,
    current_generation_step character varying(255),
    section_generation_status json,
    generation_started_at timestamp(0) without time zone,
    generation_completed_at timestamp(0) without time zone,
    author_id bigint,
    last_edited_by bigint,
    CONSTRAINT ai_contents_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'generating'::character varying, 'completed'::character varying, 'failed'::character varying])::text[])))
);


ALTER TABLE public.ai_contents OWNER TO ali_master;

--
-- Name: ai_contents_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ai_contents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ai_contents_id_seq OWNER TO ali_master;

--
-- Name: ai_contents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ai_contents_id_seq OWNED BY public.ai_contents.id;


--
-- Name: ai_search_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ai_search_logs (
    id bigint NOT NULL,
    query text NOT NULL,
    type character varying(255) DEFAULT 'text'::character varying NOT NULL,
    user_id bigint,
    session_id character varying(255),
    ip_address inet,
    user_agent text,
    results_count integer DEFAULT 0 NOT NULL,
    intent character varying(255),
    confidence numeric(3,2),
    cached boolean DEFAULT false NOT NULL,
    response_time_ms integer,
    metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT ai_search_logs_type_check CHECK (((type)::text = ANY ((ARRAY['text'::character varying, 'voice'::character varying, 'image'::character varying, 'conversational'::character varying])::text[])))
);


ALTER TABLE public.ai_search_logs OWNER TO ali_master;

--
-- Name: ai_search_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ai_search_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ai_search_logs_id_seq OWNER TO ali_master;

--
-- Name: ai_search_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ai_search_logs_id_seq OWNED BY public.ai_search_logs.id;


--
-- Name: ai_settings; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ai_settings (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    is_active boolean DEFAULT true NOT NULL,
    model_config json,
    generation_settings json,
    prompt_templates json,
    language_settings json,
    tone_settings json,
    content_formats json,
    target_audiences json,
    custom_instructions json,
    max_tokens integer DEFAULT 2048 NOT NULL,
    temperature double precision DEFAULT '0.7'::double precision NOT NULL,
    frequency_penalty double precision DEFAULT '0'::double precision NOT NULL,
    presence_penalty double precision DEFAULT '0'::double precision NOT NULL,
    stop_sequences json,
    ordering integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.ai_settings OWNER TO ali_master;

--
-- Name: ai_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ai_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ai_settings_id_seq OWNER TO ali_master;

--
-- Name: ai_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ai_settings_id_seq OWNED BY public.ai_settings.id;


--
-- Name: api_tokens; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.api_tokens (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token_hash character varying(64) NOT NULL,
    permissions json DEFAULT '[]'::json NOT NULL,
    expires_at timestamp(0) without time zone,
    last_used_at timestamp(0) without time zone,
    created_by bigint,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.api_tokens OWNER TO ali_master;

--
-- Name: api_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.api_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.api_tokens_id_seq OWNER TO ali_master;

--
-- Name: api_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.api_tokens_id_seq OWNED BY public.api_tokens.id;


--
-- Name: auto_response_contexts; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.auto_response_contexts (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    keywords text,
    example_queries text,
    is_active boolean DEFAULT true NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    confidence_threshold double precision DEFAULT '0.7'::double precision NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.auto_response_contexts OWNER TO ali_master;

--
-- Name: COLUMN auto_response_contexts.name; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.name IS 'Name of the context/category';


--
-- Name: COLUMN auto_response_contexts.description; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.description IS 'Description of what this context covers';


--
-- Name: COLUMN auto_response_contexts.keywords; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.keywords IS 'Keywords that help identify this context';


--
-- Name: COLUMN auto_response_contexts.example_queries; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.example_queries IS 'Example user queries for this context';


--
-- Name: COLUMN auto_response_contexts.priority; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.priority IS 'Higher priority contexts are checked first';


--
-- Name: COLUMN auto_response_contexts.confidence_threshold; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_contexts.confidence_threshold IS 'Minimum confidence score for matching (0-1)';


--
-- Name: auto_response_contexts_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.auto_response_contexts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auto_response_contexts_id_seq OWNER TO ali_master;

--
-- Name: auto_response_contexts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.auto_response_contexts_id_seq OWNED BY public.auto_response_contexts.id;


--
-- Name: auto_response_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.auto_response_logs (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    context_id bigint,
    response_id bigint,
    user_query text NOT NULL,
    ai_analysis json,
    confidence_score double precision,
    was_helpful boolean,
    user_feedback text,
    escalated_to_support boolean DEFAULT false NOT NULL,
    responded_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.auto_response_logs OWNER TO ali_master;

--
-- Name: COLUMN auto_response_logs.user_query; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.user_query IS 'The original user query';


--
-- Name: COLUMN auto_response_logs.ai_analysis; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.ai_analysis IS 'AI analysis results including confidence scores';


--
-- Name: COLUMN auto_response_logs.confidence_score; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.confidence_score IS 'Confidence score of the match';


--
-- Name: COLUMN auto_response_logs.was_helpful; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.was_helpful IS 'User feedback on whether response was helpful';


--
-- Name: COLUMN auto_response_logs.user_feedback; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.user_feedback IS 'Additional user feedback';


--
-- Name: COLUMN auto_response_logs.escalated_to_support; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_response_logs.escalated_to_support IS 'Whether ticket was escalated to support';


--
-- Name: auto_response_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.auto_response_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auto_response_logs_id_seq OWNER TO ali_master;

--
-- Name: auto_response_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.auto_response_logs_id_seq OWNED BY public.auto_response_logs.id;


--
-- Name: auto_responses; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.auto_responses (
    id bigint NOT NULL,
    context_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    response_text text NOT NULL,
    attachments json,
    links json,
    is_active boolean DEFAULT true NOT NULL,
    mark_as_resolved boolean DEFAULT false NOT NULL,
    language character varying(255) DEFAULT 'fa'::character varying NOT NULL,
    usage_count integer DEFAULT 0 NOT NULL,
    satisfaction_score double precision,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.auto_responses OWNER TO ali_master;

--
-- Name: COLUMN auto_responses.title; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.title IS 'Title of the response';


--
-- Name: COLUMN auto_responses.response_text; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.response_text IS 'The actual response to send';


--
-- Name: COLUMN auto_responses.attachments; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.attachments IS 'File paths of attachments to include';


--
-- Name: COLUMN auto_responses.links; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.links IS 'Helpful links to include';


--
-- Name: COLUMN auto_responses.mark_as_resolved; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.mark_as_resolved IS 'Should ticket be marked as resolved after this response';


--
-- Name: COLUMN auto_responses.language; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.language IS 'Language of the response (fa/en)';


--
-- Name: COLUMN auto_responses.usage_count; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.usage_count IS 'How many times this response has been used';


--
-- Name: COLUMN auto_responses.satisfaction_score; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.auto_responses.satisfaction_score IS 'Average satisfaction score for this response';


--
-- Name: auto_responses_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.auto_responses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auto_responses_id_seq OWNER TO ali_master;

--
-- Name: auto_responses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.auto_responses_id_seq OWNED BY public.auto_responses.id;


--
-- Name: banks; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.banks (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    en_name character varying(255) NOT NULL,
    bank_id character varying(255) NOT NULL,
    logo character varying(255) NOT NULL,
    card_prefixes json NOT NULL,
    color character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.banks OWNER TO ali_master;

--
-- Name: banks_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.banks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.banks_id_seq OWNER TO ali_master;

--
-- Name: banks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.banks_id_seq OWNED BY public.banks.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO ali_master;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO ali_master;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    og_title character varying(255),
    og_description text,
    og_image character varying(255),
    twitter_title character varying(255),
    twitter_description text,
    twitter_image character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.categories OWNER TO ali_master;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO ali_master;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: comments; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.comments (
    id bigint NOT NULL,
    post_id bigint NOT NULL,
    author_name character varying(255) NOT NULL,
    author_email character varying(255) NOT NULL,
    content text NOT NULL,
    meta_description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_approved boolean DEFAULT false NOT NULL,
    likes_count integer DEFAULT 0 NOT NULL,
    parent_id bigint
);


ALTER TABLE public.comments OWNER TO ali_master;

--
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comments_id_seq OWNER TO ali_master;

--
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- Name: contact_messages; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.contact_messages (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.contact_messages OWNER TO ali_master;

--
-- Name: contact_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.contact_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.contact_messages_id_seq OWNER TO ali_master;

--
-- Name: contact_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.contact_messages_id_seq OWNED BY public.contact_messages.id;


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.currencies (
    id bigint NOT NULL,
    code character varying(3) NOT NULL,
    name character varying(255) NOT NULL,
    symbol character varying(10) NOT NULL,
    exchange_rate numeric(20,8) DEFAULT '1'::numeric NOT NULL,
    is_base_currency boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    decimal_places integer DEFAULT 2 NOT NULL,
    "position" character varying(10) DEFAULT 'before'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.currencies OWNER TO ali_master;

--
-- Name: currencies_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.currencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.currencies_id_seq OWNER TO ali_master;

--
-- Name: currencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.currencies_id_seq OWNED BY public.currencies.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO ali_master;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO ali_master;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: filament_filter_set_user; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.filament_filter_set_user (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    filter_set_id bigint NOT NULL,
    sort_order smallint DEFAULT '1'::smallint NOT NULL,
    is_visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.filament_filter_set_user OWNER TO ali_master;

--
-- Name: filament_filter_set_user_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.filament_filter_set_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.filament_filter_set_user_id_seq OWNER TO ali_master;

--
-- Name: filament_filter_set_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.filament_filter_set_user_id_seq OWNED BY public.filament_filter_set_user.id;


--
-- Name: filament_filter_sets; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.filament_filter_sets (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    resource character varying(255) NOT NULL,
    filters text NOT NULL,
    indicators json NOT NULL,
    is_public boolean NOT NULL,
    is_global_favorite boolean NOT NULL,
    sort_order smallint DEFAULT '1'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    color character varying(255),
    icon character varying(255),
    status character varying(255) DEFAULT 'approved'::character varying NOT NULL,
    tenant_id integer
);


ALTER TABLE public.filament_filter_sets OWNER TO ali_master;

--
-- Name: filament_filter_sets_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.filament_filter_sets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.filament_filter_sets_id_seq OWNER TO ali_master;

--
-- Name: filament_filter_sets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.filament_filter_sets_id_seq OWNED BY public.filament_filter_sets.id;


--
-- Name: filament_filter_sets_managed_preset_views; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.filament_filter_sets_managed_preset_views (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    label character varying(255),
    resource character varying(255) NOT NULL,
    is_favorite boolean DEFAULT true NOT NULL,
    sort_order smallint DEFAULT '1'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tenant_id integer
);


ALTER TABLE public.filament_filter_sets_managed_preset_views OWNER TO ali_master;

--
-- Name: filament_filter_sets_managed_preset_views_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.filament_filter_sets_managed_preset_views_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.filament_filter_sets_managed_preset_views_id_seq OWNER TO ali_master;

--
-- Name: filament_filter_sets_managed_preset_views_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.filament_filter_sets_managed_preset_views_id_seq OWNED BY public.filament_filter_sets_managed_preset_views.id;


--
-- Name: footer_contents; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.footer_contents (
    id bigint NOT NULL,
    key character varying(255) NOT NULL,
    value text NOT NULL,
    type character varying(255) DEFAULT 'text'::character varying NOT NULL,
    section character varying(255) DEFAULT 'general'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    settings json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.footer_contents OWNER TO ali_master;

--
-- Name: footer_contents_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.footer_contents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.footer_contents_id_seq OWNER TO ali_master;

--
-- Name: footer_contents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.footer_contents_id_seq OWNED BY public.footer_contents.id;


--
-- Name: footer_links; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.footer_links (
    id bigint NOT NULL,
    footer_section_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    url character varying(255) NOT NULL,
    icon character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    open_in_new_tab boolean DEFAULT false NOT NULL,
    target character varying(255) DEFAULT '_self'::character varying NOT NULL,
    attributes json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.footer_links OWNER TO ali_master;

--
-- Name: footer_links_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.footer_links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.footer_links_id_seq OWNER TO ali_master;

--
-- Name: footer_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.footer_links_id_seq OWNED BY public.footer_links.id;


--
-- Name: footer_sections; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.footer_sections (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    icon character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    location character varying(255) DEFAULT 'footer'::character varying NOT NULL,
    settings json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.footer_sections OWNER TO ali_master;

--
-- Name: footer_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.footer_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.footer_sections_id_seq OWNER TO ali_master;

--
-- Name: footer_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.footer_sections_id_seq OWNED BY public.footer_sections.id;


--
-- Name: gateway_transaction_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.gateway_transaction_logs (
    id bigint NOT NULL,
    gateway_transaction_id bigint NOT NULL,
    action character varying(255) NOT NULL,
    source character varying(255) NOT NULL,
    message text,
    data json,
    request_data json,
    response_data json,
    ip_address character varying(45),
    user_agent text,
    method character varying(10),
    url character varying(255),
    headers json,
    error_code character varying(255),
    error_message text,
    stack_trace text,
    response_time_ms integer,
    memory_usage_mb integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.gateway_transaction_logs OWNER TO ali_master;

--
-- Name: gateway_transaction_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.gateway_transaction_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gateway_transaction_logs_id_seq OWNER TO ali_master;

--
-- Name: gateway_transaction_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.gateway_transaction_logs_id_seq OWNED BY public.gateway_transaction_logs.id;


--
-- Name: gateway_transactions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.gateway_transactions (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    user_id bigint,
    payment_gateway_id bigint NOT NULL,
    currency_id bigint NOT NULL,
    amount bigint NOT NULL,
    tax_amount bigint DEFAULT '0'::bigint NOT NULL,
    gateway_fee bigint DEFAULT '0'::bigint NOT NULL,
    total_amount bigint NOT NULL,
    gateway_transaction_id character varying(255),
    gateway_reference character varying(255),
    gateway_response json,
    type character varying(255) DEFAULT 'payment'::character varying NOT NULL,
    status character varying(255) NOT NULL,
    description text,
    metadata json,
    user_ip character varying(45),
    user_agent text,
    user_country character varying(2),
    user_device character varying(255),
    processed_at timestamp(0) without time zone,
    completed_at timestamp(0) without time zone,
    failed_at timestamp(0) without time zone,
    expired_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.gateway_transactions OWNER TO ali_master;

--
-- Name: gateway_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.gateway_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gateway_transactions_id_seq OWNER TO ali_master;

--
-- Name: gateway_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.gateway_transactions_id_seq OWNED BY public.gateway_transactions.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO ali_master;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO ali_master;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO ali_master;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: media; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.media (
    id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    uuid uuid,
    collection_name character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    file_name character varying(255) NOT NULL,
    mime_type character varying(255),
    disk character varying(255) NOT NULL,
    conversions_disk character varying(255),
    size bigint NOT NULL,
    manipulations json NOT NULL,
    custom_properties json NOT NULL,
    generated_conversions json NOT NULL,
    responsive_images json NOT NULL,
    order_column integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.media OWNER TO ali_master;

--
-- Name: media_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.media_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.media_id_seq OWNER TO ali_master;

--
-- Name: media_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.media_id_seq OWNED BY public.media.id;


--
-- Name: meta; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.meta (
    id bigint NOT NULL,
    metable_type character varying(255) NOT NULL,
    metable_id bigint NOT NULL,
    type character varying(255),
    key character varying(255) NOT NULL,
    value text NOT NULL,
    numeric_value numeric(36,16),
    hmac character varying(64)
);


ALTER TABLE public.meta OWNER TO ali_master;

--
-- Name: meta_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.meta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.meta_id_seq OWNER TO ali_master;

--
-- Name: meta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.meta_id_seq OWNED BY public.meta.id;


--
-- Name: metas; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.metas (
    id bigint NOT NULL,
    metable_type character varying(255) NOT NULL,
    metable_id bigint NOT NULL,
    key character varying(255) NOT NULL,
    value text NOT NULL,
    type character varying(255) DEFAULT 'string'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.metas OWNER TO ali_master;

--
-- Name: metas_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.metas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.metas_id_seq OWNER TO ali_master;

--
-- Name: metas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.metas_id_seq OWNED BY public.metas.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO ali_master;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO ali_master;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO ali_master;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO ali_master;

--
-- Name: otps; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.otps (
    id bigint NOT NULL,
    mobile character varying(15) NOT NULL,
    code character varying(6) NOT NULL,
    type character varying(255) DEFAULT 'login'::character varying NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    verified_at timestamp(0) without time zone,
    is_used boolean DEFAULT false NOT NULL,
    ip_address character varying(45),
    user_agent text,
    attempts integer DEFAULT 0 NOT NULL,
    last_attempt_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT otps_type_check CHECK (((type)::text = ANY ((ARRAY['login'::character varying, 'register'::character varying, 'password_reset'::character varying])::text[])))
);


ALTER TABLE public.otps OWNER TO ali_master;

--
-- Name: otps_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.otps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.otps_id_seq OWNER TO ali_master;

--
-- Name: otps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.otps_id_seq OWNED BY public.otps.id;


--
-- Name: pages; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.pages (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    content text NOT NULL,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    og_title character varying(255),
    og_description text,
    og_image character varying(255),
    twitter_title character varying(255),
    twitter_description text,
    twitter_image character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.pages OWNER TO ali_master;

--
-- Name: pages_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.pages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pages_id_seq OWNER TO ali_master;

--
-- Name: pages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.pages_id_seq OWNED BY public.pages.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO ali_master;

--
-- Name: payment_gateways; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.payment_gateways (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    driver character varying(255) NOT NULL,
    description text,
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    config json NOT NULL,
    supported_currencies json,
    fee_percentage numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    fee_fixed bigint DEFAULT '0'::bigint NOT NULL,
    min_amount integer DEFAULT 0 NOT NULL,
    max_amount integer,
    logo_url character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payment_gateways OWNER TO ali_master;

--
-- Name: payment_gateways_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.payment_gateways_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payment_gateways_id_seq OWNER TO ali_master;

--
-- Name: payment_gateways_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.payment_gateways_id_seq OWNED BY public.payment_gateways.id;


--
-- Name: payment_methods; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.payment_methods (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    payment_gateway_id bigint NOT NULL,
    type character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    last_four character varying(4),
    card_type character varying(255),
    expiry_month character varying(2),
    expiry_year character varying(4),
    gateway_token character varying(255),
    gateway_data json,
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    verified_at timestamp(0) without time zone,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payment_methods OWNER TO ali_master;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.payment_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payment_methods_id_seq OWNER TO ali_master;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.payment_methods_id_seq OWNED BY public.payment_methods.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO ali_master;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO ali_master;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO ali_master;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO ali_master;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: posts; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.posts (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    content text NOT NULL,
    category_id bigint NOT NULL,
    summary text,
    description text,
    thumbnail character varying(255),
    images json,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    published_at timestamp(0) without time zone,
    featured boolean DEFAULT false NOT NULL,
    views bigint DEFAULT '0'::bigint NOT NULL,
    likes bigint DEFAULT '0'::bigint NOT NULL,
    shares bigint DEFAULT '0'::bigint NOT NULL,
    author_id bigint NOT NULL,
    ai_title character varying(255),
    ai_summary text,
    ai_description text,
    ai_thumbnail character varying(255),
    ai_images json,
    ai_headings json,
    ai_sections json,
    ai_content text,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    og_title character varying(255),
    og_description text,
    og_image character varying(255),
    twitter_title character varying(255),
    twitter_description text,
    twitter_image character varying(255),
    schema json,
    json_ld json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.posts OWNER TO ali_master;

--
-- Name: posts_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.posts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.posts_id_seq OWNER TO ali_master;

--
-- Name: posts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.posts_id_seq OWNED BY public.posts.id;


--
-- Name: redirects; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.redirects (
    id bigint NOT NULL,
    from_url character varying(500) NOT NULL,
    to_url character varying(500) NOT NULL,
    status_code integer DEFAULT 301 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_exact_match boolean DEFAULT true NOT NULL,
    description character varying(255),
    hit_count integer DEFAULT 0 NOT NULL,
    last_hit_at timestamp(0) without time zone,
    created_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.redirects OWNER TO ali_master;

--
-- Name: redirects_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.redirects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.redirects_id_seq OWNER TO ali_master;

--
-- Name: redirects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.redirects_id_seq OWNED BY public.redirects.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO ali_master;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO ali_master;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO ali_master;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: service_categories; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.service_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    background_color character varying(255) DEFAULT '#f0f9ff'::character varying NOT NULL,
    border_color character varying(255) DEFAULT '#bbf7d0'::character varying NOT NULL,
    icon_color character varying(255) DEFAULT '#10b981'::character varying NOT NULL,
    hover_border_color character varying(255) DEFAULT '#4ade80'::character varying NOT NULL,
    hover_background_color character varying(255) DEFAULT '#f0fdf4'::character varying NOT NULL,
    background_icon text,
    display_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    background_image character varying(255)
);


ALTER TABLE public.service_categories OWNER TO ali_master;

--
-- Name: service_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.service_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_categories_id_seq OWNER TO ali_master;

--
-- Name: service_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.service_categories_id_seq OWNED BY public.service_categories.id;


--
-- Name: service_requests; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.service_requests (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    user_id bigint,
    input_data json,
    status character varying(255) NOT NULL,
    payment_transaction_id character varying(255),
    processed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    request_hash character varying(64) NOT NULL,
    wallet_transaction_id bigint,
    error_message text
);


ALTER TABLE public.service_requests OWNER TO ali_master;

--
-- Name: service_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.service_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_requests_id_seq OWNER TO ali_master;

--
-- Name: service_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.service_requests_id_seq OWNED BY public.service_requests.id;


--
-- Name: service_results; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.service_results (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    result_hash character varying(16) NOT NULL,
    input_data json,
    output_data json,
    status character varying(255) DEFAULT 'processing'::character varying NOT NULL,
    error_message text,
    processed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ip_address character varying(45),
    user_agent text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    wallet_transaction_id bigint,
    user_id bigint,
    CONSTRAINT service_results_status_check CHECK (((status)::text = ANY ((ARRAY['success'::character varying, 'failed'::character varying, 'processing'::character varying])::text[])))
);


ALTER TABLE public.service_results OWNER TO ali_master;

--
-- Name: service_results_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.service_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_results_id_seq OWNER TO ali_master;

--
-- Name: service_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.service_results_id_seq OWNED BY public.service_results.id;


--
-- Name: services; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.services (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    content text NOT NULL,
    category_id bigint NOT NULL,
    summary text,
    description text,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    featured boolean DEFAULT false NOT NULL,
    author_id bigint NOT NULL,
    parent_id bigint,
    views integer DEFAULT 0 NOT NULL,
    likes integer DEFAULT 0 NOT NULL,
    shares integer DEFAULT 0 NOT NULL,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    og_title character varying(255),
    og_description text,
    og_image character varying(255),
    twitter_title character varying(255),
    twitter_description text,
    twitter_image character varying(255),
    schema json,
    faqs json,
    related_articles json,
    comment_status boolean DEFAULT true NOT NULL,
    icon character varying(255),
    published_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_paid boolean DEFAULT false NOT NULL,
    cost integer DEFAULT 0 NOT NULL,
    currency character varying(3) DEFAULT 'IRT'::character varying NOT NULL,
    price integer DEFAULT 0 NOT NULL,
    hidden_fields json,
    short_title character varying(255),
    explanation text,
    is_active boolean DEFAULT true NOT NULL,
    keywords text,
    requires_sms boolean DEFAULT false NOT NULL
);


ALTER TABLE public.services OWNER TO ali_master;

--
-- Name: COLUMN services.hidden_fields; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.services.hidden_fields IS 'Fields to hide from service results';


--
-- Name: COLUMN services.short_title; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.services.short_title IS 'Short title for use in limited space areas like homepage';


--
-- Name: COLUMN services.explanation; Type: COMMENT; Schema: public; Owner: ali_master
--

COMMENT ON COLUMN public.services.explanation IS 'Detailed explanation of the service';


--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.services_id_seq OWNER TO ali_master;

--
-- Name: services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.services_id_seq OWNED BY public.services.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO ali_master;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.settings (
    id bigint NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    type character varying(255) DEFAULT 'text'::character varying NOT NULL,
    "group" character varying(255) DEFAULT 'general'::character varying NOT NULL,
    label character varying(255) NOT NULL,
    description text,
    is_public boolean DEFAULT true NOT NULL,
    is_required boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.settings OWNER TO ali_master;

--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.settings_id_seq OWNER TO ali_master;

--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- Name: site_links; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.site_links (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    url character varying(255) NOT NULL,
    location character varying(255) NOT NULL,
    icon character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    open_in_new_tab boolean DEFAULT false NOT NULL,
    target character varying(255) DEFAULT '_self'::character varying NOT NULL,
    attributes json,
    css_class character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.site_links OWNER TO ali_master;

--
-- Name: site_links_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.site_links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.site_links_id_seq OWNER TO ali_master;

--
-- Name: site_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.site_links_id_seq OWNED BY public.site_links.id;


--
-- Name: support_agent_categories; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.support_agent_categories (
    id bigint NOT NULL,
    support_agent_id bigint NOT NULL,
    ticket_category_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.support_agent_categories OWNER TO ali_master;

--
-- Name: support_agent_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.support_agent_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.support_agent_categories_id_seq OWNER TO ali_master;

--
-- Name: support_agent_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.support_agent_categories_id_seq OWNED BY public.support_agent_categories.id;


--
-- Name: support_agents; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.support_agents (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    agent_code character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_online boolean DEFAULT false NOT NULL,
    auto_assign boolean DEFAULT true NOT NULL,
    max_tickets integer DEFAULT 10 NOT NULL,
    current_tickets integer DEFAULT 0 NOT NULL,
    specialties json,
    languages json,
    working_hours json,
    timezone character varying(255),
    last_activity_at timestamp(0) without time zone,
    response_time_avg integer,
    resolution_time_avg integer,
    satisfaction_rating numeric(3,2),
    total_tickets_handled integer DEFAULT 0 NOT NULL,
    total_tickets_resolved integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.support_agents OWNER TO ali_master;

--
-- Name: support_agents_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.support_agents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.support_agents_id_seq OWNER TO ali_master;

--
-- Name: support_agents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.support_agents_id_seq OWNED BY public.support_agents.id;


--
-- Name: taggables; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.taggables (
    tag_id bigint NOT NULL,
    taggable_type character varying(255) NOT NULL,
    taggable_id bigint NOT NULL
);


ALTER TABLE public.taggables OWNER TO ali_master;

--
-- Name: tags; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.tags (
    id bigint NOT NULL,
    name json NOT NULL,
    slug json NOT NULL,
    type character varying(255),
    order_column integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tags OWNER TO ali_master;

--
-- Name: tags_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.tags_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tags_id_seq OWNER TO ali_master;

--
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- Name: tax_rules; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.tax_rules (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    rate numeric(8,4) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    applicable_currencies json,
    min_amount bigint DEFAULT '0'::bigint NOT NULL,
    max_amount bigint,
    description text,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tax_rules OWNER TO ali_master;

--
-- Name: tax_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.tax_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tax_rules_id_seq OWNER TO ali_master;

--
-- Name: tax_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.tax_rules_id_seq OWNED BY public.tax_rules.id;


--
-- Name: telegram_admin_sessions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_admin_sessions (
    id bigint NOT NULL,
    admin_id bigint NOT NULL,
    session_token character varying(64) NOT NULL,
    ip_hash character varying(64),
    user_agent_hash character varying(64),
    expires_at timestamp(0) without time zone NOT NULL,
    last_activity_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.telegram_admin_sessions OWNER TO ali_master;

--
-- Name: telegram_admin_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_admin_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_admin_sessions_id_seq OWNER TO ali_master;

--
-- Name: telegram_admin_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_admin_sessions_id_seq OWNED BY public.telegram_admin_sessions.id;


--
-- Name: telegram_admins; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_admins (
    id bigint NOT NULL,
    telegram_user_id character varying(20) NOT NULL,
    username character varying(255),
    first_name character varying(255) NOT NULL,
    last_name character varying(255),
    role character varying(255) DEFAULT 'support'::character varying NOT NULL,
    permissions json DEFAULT '[]'::json NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    last_login_at timestamp(0) without time zone,
    failed_login_attempts integer DEFAULT 0 NOT NULL,
    locked_until timestamp(0) without time zone,
    created_by character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT telegram_admins_role_check CHECK (((role)::text = ANY ((ARRAY['super_admin'::character varying, 'admin'::character varying, 'moderator'::character varying, 'support'::character varying, 'read_only'::character varying])::text[])))
);


ALTER TABLE public.telegram_admins OWNER TO ali_master;

--
-- Name: telegram_admins_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_admins_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_admins_id_seq OWNER TO ali_master;

--
-- Name: telegram_admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_admins_id_seq OWNED BY public.telegram_admins.id;


--
-- Name: telegram_audit_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_audit_logs (
    id bigint NOT NULL,
    admin_id bigint,
    action character varying(100) NOT NULL,
    resource_type character varying(50),
    resource_id character varying(50),
    old_values json,
    new_values json,
    ip_hash character varying(64),
    user_agent_hash character varying(64),
    success boolean DEFAULT true NOT NULL,
    error_message text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.telegram_audit_logs OWNER TO ali_master;

--
-- Name: telegram_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_audit_logs_id_seq OWNER TO ali_master;

--
-- Name: telegram_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_audit_logs_id_seq OWNED BY public.telegram_audit_logs.id;


--
-- Name: telegram_posts; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_posts (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    scheduled_for timestamp(0) without time zone,
    published_at timestamp(0) without time zone,
    channel_id character varying(20),
    message_id character varying(20),
    created_by bigint NOT NULL,
    updated_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT telegram_posts_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'scheduled'::character varying, 'published'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.telegram_posts OWNER TO ali_master;

--
-- Name: telegram_posts_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_posts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_posts_id_seq OWNER TO ali_master;

--
-- Name: telegram_posts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_posts_id_seq OWNED BY public.telegram_posts.id;


--
-- Name: telegram_security_events; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_security_events (
    id bigint NOT NULL,
    event_type character varying(50) NOT NULL,
    admin_id bigint,
    telegram_user_id character varying(20),
    ip_hash character varying(64),
    details json DEFAULT '{}'::json NOT NULL,
    severity character varying(255) DEFAULT 'info'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT telegram_security_events_severity_check CHECK (((severity)::text = ANY ((ARRAY['info'::character varying, 'warning'::character varying, 'error'::character varying, 'critical'::character varying])::text[])))
);


ALTER TABLE public.telegram_security_events OWNER TO ali_master;

--
-- Name: telegram_security_events_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_security_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_security_events_id_seq OWNER TO ali_master;

--
-- Name: telegram_security_events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_security_events_id_seq OWNED BY public.telegram_security_events.id;


--
-- Name: telegram_ticket_messages; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_ticket_messages (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    user_id character varying(255) NOT NULL,
    message text NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.telegram_ticket_messages OWNER TO ali_master;

--
-- Name: telegram_ticket_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_ticket_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_ticket_messages_id_seq OWNER TO ali_master;

--
-- Name: telegram_ticket_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_ticket_messages_id_seq OWNED BY public.telegram_ticket_messages.id;


--
-- Name: telegram_tickets; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.telegram_tickets (
    id bigint NOT NULL,
    user_id character varying(255) NOT NULL,
    user_name character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'open'::character varying NOT NULL,
    priority character varying(255) DEFAULT 'normal'::character varying NOT NULL,
    assigned_to character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT telegram_tickets_priority_check CHECK (((priority)::text = ANY ((ARRAY['low'::character varying, 'normal'::character varying, 'high'::character varying, 'urgent'::character varying])::text[]))),
    CONSTRAINT telegram_tickets_status_check CHECK (((status)::text = ANY ((ARRAY['open'::character varying, 'waiting_admin'::character varying, 'waiting_user'::character varying, 'closed'::character varying])::text[])))
);


ALTER TABLE public.telegram_tickets OWNER TO ali_master;

--
-- Name: telegram_tickets_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.telegram_tickets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telegram_tickets_id_seq OWNER TO ali_master;

--
-- Name: telegram_tickets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.telegram_tickets_id_seq OWNED BY public.telegram_tickets.id;


--
-- Name: ticket_activities; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_activities (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    user_id bigint,
    action character varying(255) NOT NULL,
    description text NOT NULL,
    old_values json,
    new_values json,
    is_public boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_activities OWNER TO ali_master;

--
-- Name: ticket_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_activities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_activities_id_seq OWNER TO ali_master;

--
-- Name: ticket_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_activities_id_seq OWNED BY public.ticket_activities.id;


--
-- Name: ticket_attachments; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_attachments (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    ticket_message_id bigint,
    filename character varying(255) NOT NULL,
    original_filename character varying(255) NOT NULL,
    mime_type character varying(255) NOT NULL,
    file_size integer NOT NULL,
    file_path character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_attachments OWNER TO ali_master;

--
-- Name: ticket_attachments_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_attachments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_attachments_id_seq OWNER TO ali_master;

--
-- Name: ticket_attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_attachments_id_seq OWNED BY public.ticket_attachments.id;


--
-- Name: ticket_categories; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    color character varying(7) DEFAULT '#3B82F6'::character varying NOT NULL,
    icon character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    auto_assign_to bigint,
    required_fields json,
    estimated_response_time integer,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_categories OWNER TO ali_master;

--
-- Name: ticket_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_categories_id_seq OWNER TO ali_master;

--
-- Name: ticket_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_categories_id_seq OWNED BY public.ticket_categories.id;


--
-- Name: ticket_escalation_rules; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_escalation_rules (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    category_id bigint,
    priority_id bigint,
    trigger_after_minutes integer NOT NULL,
    trigger_condition character varying(255) NOT NULL,
    escalate_to_priority_id bigint,
    escalate_to_user_id bigint,
    send_notification boolean DEFAULT true NOT NULL,
    notification_message text,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_escalation_rules OWNER TO ali_master;

--
-- Name: ticket_escalation_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_escalation_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_escalation_rules_id_seq OWNER TO ali_master;

--
-- Name: ticket_escalation_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_escalation_rules_id_seq OWNED BY public.ticket_escalation_rules.id;


--
-- Name: ticket_messages; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_messages (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    user_id bigint NOT NULL,
    message text NOT NULL,
    is_internal boolean DEFAULT false NOT NULL,
    attachments json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    template_id bigint,
    is_system_message boolean DEFAULT false NOT NULL,
    message_data json,
    read_at timestamp(0) without time zone,
    message_type character varying(255) DEFAULT 'text'::character varying NOT NULL,
    is_auto_response boolean DEFAULT false NOT NULL
);


ALTER TABLE public.ticket_messages OWNER TO ali_master;

--
-- Name: ticket_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_messages_id_seq OWNER TO ali_master;

--
-- Name: ticket_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_messages_id_seq OWNED BY public.ticket_messages.id;


--
-- Name: ticket_priorities; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_priorities (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    color character varying(7) DEFAULT '#10B981'::character varying NOT NULL,
    level integer DEFAULT 5 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    auto_escalate_after integer,
    escalate_to_priority_id bigint,
    sort_order integer DEFAULT 0 NOT NULL,
    icon character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_priorities OWNER TO ali_master;

--
-- Name: ticket_priorities_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_priorities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_priorities_id_seq OWNER TO ali_master;

--
-- Name: ticket_priorities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_priorities_id_seq OWNED BY public.ticket_priorities.id;


--
-- Name: ticket_sla_settings; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_sla_settings (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    category_id bigint,
    priority_id bigint,
    first_response_time integer NOT NULL,
    resolution_time integer NOT NULL,
    working_hours json,
    exclude_weekends boolean DEFAULT true NOT NULL,
    excluded_dates json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_sla_settings OWNER TO ali_master;

--
-- Name: ticket_sla_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_sla_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_sla_settings_id_seq OWNER TO ali_master;

--
-- Name: ticket_sla_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_sla_settings_id_seq OWNED BY public.ticket_sla_settings.id;


--
-- Name: ticket_statuses; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_statuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    color character varying(7) DEFAULT '#3B82F6'::character varying NOT NULL,
    icon character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    is_closed boolean DEFAULT false NOT NULL,
    is_resolved boolean DEFAULT false NOT NULL,
    requires_user_action boolean DEFAULT false NOT NULL,
    auto_close_after integer,
    sort_order integer DEFAULT 0 NOT NULL,
    next_status_options json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_statuses OWNER TO ali_master;

--
-- Name: ticket_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_statuses_id_seq OWNER TO ali_master;

--
-- Name: ticket_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_statuses_id_seq OWNED BY public.ticket_statuses.id;


--
-- Name: ticket_templates; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.ticket_templates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    subject character varying(255),
    content text NOT NULL,
    category_id bigint,
    is_active boolean DEFAULT true NOT NULL,
    is_public boolean DEFAULT true NOT NULL,
    created_by bigint NOT NULL,
    variables json,
    usage_count integer DEFAULT 0 NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    auto_close_ticket boolean DEFAULT false NOT NULL,
    auto_change_status_to bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ticket_templates OWNER TO ali_master;

--
-- Name: ticket_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.ticket_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ticket_templates_id_seq OWNER TO ali_master;

--
-- Name: ticket_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.ticket_templates_id_seq OWNED BY public.ticket_templates.id;


--
-- Name: tickets; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.tickets (
    id bigint NOT NULL,
    ticket_number character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    subject character varying(255) NOT NULL,
    description text NOT NULL,
    priority character varying(255) DEFAULT 'medium'::character varying NOT NULL,
    status character varying(255) DEFAULT 'open'::character varying NOT NULL,
    category character varying(255) DEFAULT 'general'::character varying NOT NULL,
    department character varying(255) DEFAULT 'support'::character varying NOT NULL,
    assigned_to bigint,
    resolved_at timestamp(0) without time zone,
    closed_at timestamp(0) without time zone,
    response_time integer,
    resolution_time integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    category_id bigint,
    priority_id bigint,
    status_id bigint,
    custom_fields json,
    escalation_count integer DEFAULT 0 NOT NULL,
    escalated_at timestamp(0) without time zone,
    escalated_from_priority_id bigint,
    first_response_at timestamp(0) without time zone,
    customer_satisfaction_rating integer,
    customer_satisfaction_comment text,
    tags json,
    is_auto_responded boolean DEFAULT false NOT NULL,
    auto_response_id bigint,
    auto_responded_at timestamp(0) without time zone,
    ticket_hash character varying(32),
    CONSTRAINT tickets_category_check CHECK (((category)::text = ANY ((ARRAY['technical'::character varying, 'billing'::character varying, 'general'::character varying, 'bug_report'::character varying, 'feature_request'::character varying])::text[]))),
    CONSTRAINT tickets_priority_check CHECK (((priority)::text = ANY ((ARRAY['low'::character varying, 'medium'::character varying, 'high'::character varying, 'urgent'::character varying])::text[]))),
    CONSTRAINT tickets_status_check CHECK (((status)::text = ANY ((ARRAY['open'::character varying, 'in_progress'::character varying, 'waiting_for_user'::character varying, 'resolved'::character varying, 'closed'::character varying])::text[])))
);


ALTER TABLE public.tickets OWNER TO ali_master;

--
-- Name: tickets_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.tickets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tickets_id_seq OWNER TO ali_master;

--
-- Name: tickets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.tickets_id_seq OWNED BY public.tickets.id;


--
-- Name: token_refresh_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.token_refresh_logs (
    id bigint NOT NULL,
    provider character varying(255) NOT NULL,
    token_name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    trigger_type character varying(255) DEFAULT 'automatic'::character varying NOT NULL,
    message text,
    metadata json,
    started_at timestamp(0) without time zone,
    completed_at timestamp(0) without time zone,
    duration_ms integer,
    error_code character varying(255),
    error_details text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT token_refresh_logs_status_check CHECK (((status)::text = ANY ((ARRAY['success'::character varying, 'failed'::character varying, 'skipped'::character varying])::text[]))),
    CONSTRAINT token_refresh_logs_trigger_type_check CHECK (((trigger_type)::text = ANY ((ARRAY['automatic'::character varying, 'manual'::character varying, 'forced'::character varying])::text[])))
);


ALTER TABLE public.token_refresh_logs OWNER TO ali_master;

--
-- Name: token_refresh_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.token_refresh_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.token_refresh_logs_id_seq OWNER TO ali_master;

--
-- Name: token_refresh_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.token_refresh_logs_id_seq OWNED BY public.token_refresh_logs.id;


--
-- Name: tokens; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.tokens (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    provider character varying(255) NOT NULL,
    access_token text NOT NULL,
    refresh_token text NOT NULL,
    expires_at timestamp(0) without time zone,
    refresh_expires_at timestamp(0) without time zone,
    last_used_at timestamp(0) without time zone,
    metadata json,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tokens OWNER TO ali_master;

--
-- Name: tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tokens_id_seq OWNER TO ali_master;

--
-- Name: tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.tokens_id_seq OWNED BY public.tokens.id;


--
-- Name: transactions; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.transactions (
    id bigint NOT NULL,
    payable_type character varying(255) NOT NULL,
    payable_id bigint NOT NULL,
    wallet_id bigint NOT NULL,
    type character varying(255) NOT NULL,
    amount numeric(64,0) NOT NULL,
    confirmed boolean NOT NULL,
    meta json,
    uuid uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) with time zone,
    CONSTRAINT transactions_type_check CHECK (((type)::text = ANY ((ARRAY['deposit'::character varying, 'withdraw'::character varying])::text[])))
);


ALTER TABLE public.transactions OWNER TO ali_master;

--
-- Name: transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transactions_id_seq OWNER TO ali_master;

--
-- Name: transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.transactions_id_seq OWNED BY public.transactions.id;


--
-- Name: transfers; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.transfers (
    id bigint NOT NULL,
    from_id bigint NOT NULL,
    to_id bigint NOT NULL,
    status character varying(255) DEFAULT 'transfer'::character varying NOT NULL,
    status_last character varying(255),
    deposit_id bigint NOT NULL,
    withdraw_id bigint NOT NULL,
    discount numeric(64,0) DEFAULT '0'::numeric NOT NULL,
    fee numeric(64,0) DEFAULT '0'::numeric NOT NULL,
    uuid uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) with time zone,
    extra json,
    CONSTRAINT transfers_status_check CHECK (((status)::text = ANY ((ARRAY['exchange'::character varying, 'transfer'::character varying, 'paid'::character varying, 'refund'::character varying, 'gift'::character varying])::text[]))),
    CONSTRAINT transfers_status_last_check CHECK (((status_last)::text = ANY ((ARRAY['exchange'::character varying, 'transfer'::character varying, 'paid'::character varying, 'refund'::character varying, 'gift'::character varying])::text[])))
);


ALTER TABLE public.transfers OWNER TO ali_master;

--
-- Name: transfers_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.transfers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transfers_id_seq OWNER TO ali_master;

--
-- Name: transfers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.transfers_id_seq OWNED BY public.transfers.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255),
    email_verified_at timestamp(0) without time zone,
    password character varying(255),
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    google_id character varying(255),
    github_id character varying(255),
    facebook_id character varying(255),
    discord_id character varying(255),
    mobile character varying(15),
    mobile_verified_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO ali_master;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO ali_master;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: wallet_audit_logs; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.wallet_audit_logs (
    id bigint NOT NULL,
    wallet_id bigint NOT NULL,
    admin_id bigint,
    action character varying(50) NOT NULL,
    amount numeric(20,8),
    old_balance numeric(20,8),
    new_balance numeric(20,8),
    reason text,
    reference_id character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.wallet_audit_logs OWNER TO ali_master;

--
-- Name: wallet_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.wallet_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wallet_audit_logs_id_seq OWNER TO ali_master;

--
-- Name: wallet_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.wallet_audit_logs_id_seq OWNED BY public.wallet_audit_logs.id;


--
-- Name: wallets; Type: TABLE; Schema: public; Owner: ali_master
--

CREATE TABLE public.wallets (
    id bigint NOT NULL,
    holder_type character varying(255) NOT NULL,
    holder_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    uuid uuid NOT NULL,
    description character varying(255),
    meta json,
    balance numeric(64,0) DEFAULT '0'::numeric NOT NULL,
    decimal_places smallint DEFAULT '2'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) with time zone
);


ALTER TABLE public.wallets OWNER TO ali_master;

--
-- Name: wallets_id_seq; Type: SEQUENCE; Schema: public; Owner: ali_master
--

CREATE SEQUENCE public.wallets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wallets_id_seq OWNER TO ali_master;

--
-- Name: wallets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ali_master
--

ALTER SEQUENCE public.wallets_id_seq OWNED BY public.wallets.id;


--
-- Name: ai_content_templates id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_content_templates ALTER COLUMN id SET DEFAULT nextval('public.ai_content_templates_id_seq'::regclass);


--
-- Name: ai_contents id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_contents ALTER COLUMN id SET DEFAULT nextval('public.ai_contents_id_seq'::regclass);


--
-- Name: ai_search_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_search_logs ALTER COLUMN id SET DEFAULT nextval('public.ai_search_logs_id_seq'::regclass);


--
-- Name: ai_settings id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_settings ALTER COLUMN id SET DEFAULT nextval('public.ai_settings_id_seq'::regclass);


--
-- Name: api_tokens id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.api_tokens ALTER COLUMN id SET DEFAULT nextval('public.api_tokens_id_seq'::regclass);


--
-- Name: auto_response_contexts id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_contexts ALTER COLUMN id SET DEFAULT nextval('public.auto_response_contexts_id_seq'::regclass);


--
-- Name: auto_response_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_logs ALTER COLUMN id SET DEFAULT nextval('public.auto_response_logs_id_seq'::regclass);


--
-- Name: auto_responses id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_responses ALTER COLUMN id SET DEFAULT nextval('public.auto_responses_id_seq'::regclass);


--
-- Name: banks id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.banks ALTER COLUMN id SET DEFAULT nextval('public.banks_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- Name: contact_messages id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.contact_messages ALTER COLUMN id SET DEFAULT nextval('public.contact_messages_id_seq'::regclass);


--
-- Name: currencies id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.currencies ALTER COLUMN id SET DEFAULT nextval('public.currencies_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: filament_filter_set_user id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_set_user ALTER COLUMN id SET DEFAULT nextval('public.filament_filter_set_user_id_seq'::regclass);


--
-- Name: filament_filter_sets id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets ALTER COLUMN id SET DEFAULT nextval('public.filament_filter_sets_id_seq'::regclass);


--
-- Name: filament_filter_sets_managed_preset_views id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets_managed_preset_views ALTER COLUMN id SET DEFAULT nextval('public.filament_filter_sets_managed_preset_views_id_seq'::regclass);


--
-- Name: footer_contents id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_contents ALTER COLUMN id SET DEFAULT nextval('public.footer_contents_id_seq'::regclass);


--
-- Name: footer_links id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_links ALTER COLUMN id SET DEFAULT nextval('public.footer_links_id_seq'::regclass);


--
-- Name: footer_sections id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_sections ALTER COLUMN id SET DEFAULT nextval('public.footer_sections_id_seq'::regclass);


--
-- Name: gateway_transaction_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transaction_logs ALTER COLUMN id SET DEFAULT nextval('public.gateway_transaction_logs_id_seq'::regclass);


--
-- Name: gateway_transactions id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions ALTER COLUMN id SET DEFAULT nextval('public.gateway_transactions_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: media id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.media ALTER COLUMN id SET DEFAULT nextval('public.media_id_seq'::regclass);


--
-- Name: meta id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.meta ALTER COLUMN id SET DEFAULT nextval('public.meta_id_seq'::regclass);


--
-- Name: metas id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.metas ALTER COLUMN id SET DEFAULT nextval('public.metas_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: otps id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.otps ALTER COLUMN id SET DEFAULT nextval('public.otps_id_seq'::regclass);


--
-- Name: pages id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.pages ALTER COLUMN id SET DEFAULT nextval('public.pages_id_seq'::regclass);


--
-- Name: payment_gateways id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_gateways ALTER COLUMN id SET DEFAULT nextval('public.payment_gateways_id_seq'::regclass);


--
-- Name: payment_methods id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_methods ALTER COLUMN id SET DEFAULT nextval('public.payment_methods_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: posts id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.posts ALTER COLUMN id SET DEFAULT nextval('public.posts_id_seq'::regclass);


--
-- Name: redirects id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.redirects ALTER COLUMN id SET DEFAULT nextval('public.redirects_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: service_categories id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_categories ALTER COLUMN id SET DEFAULT nextval('public.service_categories_id_seq'::regclass);


--
-- Name: service_requests id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests ALTER COLUMN id SET DEFAULT nextval('public.service_requests_id_seq'::regclass);


--
-- Name: service_results id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results ALTER COLUMN id SET DEFAULT nextval('public.service_results_id_seq'::regclass);


--
-- Name: services id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services ALTER COLUMN id SET DEFAULT nextval('public.services_id_seq'::regclass);


--
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- Name: site_links id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.site_links ALTER COLUMN id SET DEFAULT nextval('public.site_links_id_seq'::regclass);


--
-- Name: support_agent_categories id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agent_categories ALTER COLUMN id SET DEFAULT nextval('public.support_agent_categories_id_seq'::regclass);


--
-- Name: support_agents id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agents ALTER COLUMN id SET DEFAULT nextval('public.support_agents_id_seq'::regclass);


--
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- Name: tax_rules id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tax_rules ALTER COLUMN id SET DEFAULT nextval('public.tax_rules_id_seq'::regclass);


--
-- Name: telegram_admin_sessions id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admin_sessions ALTER COLUMN id SET DEFAULT nextval('public.telegram_admin_sessions_id_seq'::regclass);


--
-- Name: telegram_admins id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admins ALTER COLUMN id SET DEFAULT nextval('public.telegram_admins_id_seq'::regclass);


--
-- Name: telegram_audit_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.telegram_audit_logs_id_seq'::regclass);


--
-- Name: telegram_posts id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_posts ALTER COLUMN id SET DEFAULT nextval('public.telegram_posts_id_seq'::regclass);


--
-- Name: telegram_security_events id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_security_events ALTER COLUMN id SET DEFAULT nextval('public.telegram_security_events_id_seq'::regclass);


--
-- Name: telegram_ticket_messages id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_ticket_messages ALTER COLUMN id SET DEFAULT nextval('public.telegram_ticket_messages_id_seq'::regclass);


--
-- Name: telegram_tickets id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_tickets ALTER COLUMN id SET DEFAULT nextval('public.telegram_tickets_id_seq'::regclass);


--
-- Name: ticket_activities id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_activities ALTER COLUMN id SET DEFAULT nextval('public.ticket_activities_id_seq'::regclass);


--
-- Name: ticket_attachments id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_attachments ALTER COLUMN id SET DEFAULT nextval('public.ticket_attachments_id_seq'::regclass);


--
-- Name: ticket_categories id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_categories ALTER COLUMN id SET DEFAULT nextval('public.ticket_categories_id_seq'::regclass);


--
-- Name: ticket_escalation_rules id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules ALTER COLUMN id SET DEFAULT nextval('public.ticket_escalation_rules_id_seq'::regclass);


--
-- Name: ticket_messages id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_messages ALTER COLUMN id SET DEFAULT nextval('public.ticket_messages_id_seq'::regclass);


--
-- Name: ticket_priorities id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_priorities ALTER COLUMN id SET DEFAULT nextval('public.ticket_priorities_id_seq'::regclass);


--
-- Name: ticket_sla_settings id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_sla_settings ALTER COLUMN id SET DEFAULT nextval('public.ticket_sla_settings_id_seq'::regclass);


--
-- Name: ticket_statuses id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_statuses ALTER COLUMN id SET DEFAULT nextval('public.ticket_statuses_id_seq'::regclass);


--
-- Name: ticket_templates id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates ALTER COLUMN id SET DEFAULT nextval('public.ticket_templates_id_seq'::regclass);


--
-- Name: tickets id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets ALTER COLUMN id SET DEFAULT nextval('public.tickets_id_seq'::regclass);


--
-- Name: token_refresh_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.token_refresh_logs ALTER COLUMN id SET DEFAULT nextval('public.token_refresh_logs_id_seq'::regclass);


--
-- Name: tokens id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tokens ALTER COLUMN id SET DEFAULT nextval('public.tokens_id_seq'::regclass);


--
-- Name: transactions id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transactions ALTER COLUMN id SET DEFAULT nextval('public.transactions_id_seq'::regclass);


--
-- Name: transfers id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transfers ALTER COLUMN id SET DEFAULT nextval('public.transfers_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: wallet_audit_logs id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallet_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.wallet_audit_logs_id_seq'::regclass);


--
-- Name: wallets id; Type: DEFAULT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallets ALTER COLUMN id SET DEFAULT nextval('public.wallets_id_seq'::regclass);


--
-- Data for Name: ai_content_templates; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ai_content_templates (id, name, prompt_template, parameters, category, is_active, usage_count, created_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ai_contents; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ai_contents (id, title, slug, short_description, language, model_type, status, ai_headings, ai_sections, meta_title, meta_description, meta_keywords, og_title, og_description, twitter_title, twitter_description, schema, json_ld, faq, created_at, updated_at, ai_summary, ai_thumbnails, model_id, generation_settings, generation_progress, current_generation_step, section_generation_status, generation_started_at, generation_completed_at, author_id, last_edited_by) FROM stdin;
\.


--
-- Data for Name: ai_search_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ai_search_logs (id, query, type, user_id, session_id, ip_address, user_agent, results_count, intent, confidence, cached, response_time_ms, metadata, created_at, updated_at) FROM stdin;
1	تست	text	\N	\N	\N	\N	0	\N	\N	f	\N	\N	2025-07-13 09:42:18	2025-07-13 09:42:18
2	تست خودرو	text	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 09:42:36	2025-07-13 09:42:36
3	چطور خلافی خودرو چک کنم؟	text	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 09:43:16	2025-07-13 09:43:16
4	سلام، چطور می‌تونم خلافی ماشینم رو چک کنم؟	conversational	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 09:43:30	2025-07-13 09:43:30
5	سلام وقت بخیر	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:26:02	2025-07-13 10:26:02
6	تبدیل شماره کارت به شبا	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:26:17	2025-07-13 10:26:17
7	تبدیل شماره کارت به شبا	conversational	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 10:29:04	2025-07-13 10:29:04
8	شبا بانک ملی	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:29:43	2025-07-13 10:29:43
9	چطور شماره کارت رو به شبا تبدیل کنم؟	text	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 10:29:45	2025-07-13 10:29:45
10	۴۷۵۴۳۲۳۴۲۳۴۲۳۴	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:30:01	2025-07-13 10:30:01
11	استعلام خلافی موتور چطوری انجام میشه؟	text	\N	\N	130.193.77.98	\N	0	\N	\N	f	\N	\N	2025-07-13 10:30:33	2025-07-13 10:30:33
12	چطور خلافی خودرو چک کنم؟	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:30:52	2025-07-13 10:30:52
13	کد پستی چطور پیدا کنم؟	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:31:42	2025-07-13 10:31:42
14	شبا چیست؟	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:31:56	2025-07-13 10:31:56
15	اوضاع ایران الان خوبه؟	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:32:33	2025-07-13 10:32:33
16	اسمت چیه؟	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:32:44	2025-07-13 10:32:44
17	میتونی برام مقاله بنویسی	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:33:07	2025-07-13 10:33:07
18	استعلام خلافی خودرو	image	\N	\N	37.40.14.214	\N	1	\N	\N	f	\N	\N	2025-07-13 10:33:30	2025-07-13 10:33:30
19	عکس چی بود فرستادم برات	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:33:45	2025-07-13 10:33:45
20	اوضاع قمر در عقربه خدایی	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:34:05	2025-07-13 10:34:05
21	تبدیل کارت به شبا	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:53:38	2025-07-13 10:53:38
22	پیشخوانک	conversational	\N	\N	37.40.14.214	\N	0	\N	\N	f	\N	\N	2025-07-13 10:53:55	2025-07-13 10:53:55
\.


--
-- Data for Name: ai_settings; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ai_settings (id, name, description, is_active, model_config, generation_settings, prompt_templates, language_settings, tone_settings, content_formats, target_audiences, custom_instructions, max_tokens, temperature, frequency_penalty, presence_penalty, stop_sequences, ordering, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: api_tokens; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.api_tokens (id, name, token_hash, permissions, expires_at, last_used_at, created_by, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: auto_response_contexts; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.auto_response_contexts (id, name, description, keywords, example_queries, is_active, priority, confidence_threshold, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: auto_response_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.auto_response_logs (id, ticket_id, context_id, response_id, user_query, ai_analysis, confidence_score, was_helpful, user_feedback, escalated_to_support, responded_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: auto_responses; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.auto_responses (id, context_id, title, response_text, attachments, links, is_active, mark_as_resolved, language, usage_count, satisfaction_score, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: banks; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.banks (id, name, en_name, bank_id, logo, card_prefixes, color, is_active, created_at, updated_at) FROM stdin;
1	بانک انصار	ansar	063	/assets/images/banks/ansar.svg	["627381"]	#c8393b	t	2025-06-29 12:37:55	2025-06-29 12:37:55
2	بانک آینده	ayandeh	062	/assets/images/banks/ayandeh.svg	["636214"]	#492631	t	2025-06-29 12:37:55	2025-06-29 12:37:55
4	بانک دی	dey	066	/assets/images/banks/day.svg	["502938"]	#008a9f	t	2025-06-29 12:37:55	2025-06-29 12:37:55
5	بانک اقتصاد نوین	eghtesad_novin	055	/assets/images/banks/eghtesad.svg	["627412"]	#5c2e91	t	2025-06-29 12:37:55	2025-06-29 12:37:55
6	بانک گردشگری	gardeshgari	064	/assets/images/banks/gardeshgari.svg	["505416"]	#af0a0f	t	2025-06-29 12:37:55	2025-06-29 12:37:55
7	بانک قوامین	ghavvamin	052	/assets/images/banks/ghavvamin.svg	["639599"]	#0e8a42	t	2025-06-29 12:37:55	2025-06-29 12:37:55
8	بانک کارآفرین	karafarin	053	/assets/images/banks/karafarin.svg	["627488","502910"]	#168474	t	2025-06-29 12:37:55	2025-06-29 12:37:55
9	بانک کشاورزی	keshavarzi	016	/assets/images/banks/keshavarzi.svg	["603770","639217"]	#112c09	t	2025-06-29 12:37:55	2025-06-29 12:37:55
10	بانک مسکن	maskan	014	/assets/images/banks/maskan.svg	["628023"]	#ff0100	t	2025-06-29 12:37:55	2025-06-29 12:37:55
11	بانک مهر اقتصاد	mehr_e_eghtesad	079	/assets/images/banks/mehreghtesad.svg	["639370"]	#00a653	t	2025-06-29 12:37:55	2025-06-29 12:37:55
12	بانک قرض الحسنه مهر ایران	mehr_e_iranian	060	/assets/images/banks/mehriran.svg	["606373"]	#00a653	t	2025-06-29 12:37:55	2025-06-29 12:37:55
13	بانک ملی ایران	meli	017	/assets/images/banks/melli.svg	["603799"]	#202f5b	t	2025-06-29 12:37:55	2025-06-29 12:37:55
14	بانک ملت	mellat	012	/assets/images/banks/mellat.svg	["610433","991975"]	#d12236	t	2025-06-29 12:37:55	2025-06-29 12:37:55
15	موسسه اعتباری ملل	melal	075	/assets/images/banks/melal.svg	["606256"]	#37389a	t	2025-06-29 12:37:55	2025-06-29 12:37:55
16	بانک پارسیان	parsian	054	/assets/images/banks/parsian.svg	["622106","639194","627884"]	#a10f1f	t	2025-06-29 12:37:55	2025-06-29 12:37:55
17	بانک پاسارگاد	pasargad	057	/assets/images/banks/pasargad.svg	["502229","639347"]	#ffc110	t	2025-06-29 12:37:55	2025-06-29 12:37:55
18	پست بانک ایران	post_bank	021	/assets/images/banks/post.svg	["627760"]	#008840	t	2025-06-29 12:37:55	2025-06-29 12:37:55
19	بانک رفاه کارگران	refah	013	/assets/images/banks/refahkargaran.svg	["589463"]	#1e7a00	t	2025-06-29 12:37:55	2025-06-29 12:37:55
20	بانک صنعت و معدن	sanat_va_maadan	011	/assets/images/banks/sanatmadan.svg	["627961"]	#0f317e	t	2025-06-29 12:37:55	2025-06-29 12:37:55
21	بانک صادرات	saderat	019	/assets/images/banks/saderat.svg	["603769"]	#29166f	t	2025-06-29 12:37:55	2025-06-29 12:37:55
3	بانک سامان	saman	056	/assets/images/banks/saman.svg	["621986"]	#00aae8	t	2025-06-29 12:37:55	2025-06-29 12:37:55
22	بانک سرمایه	sarmayeh	058	/assets/images/banks/sarmaye.svg	["639607"]	#a7a7a7	t	2025-06-29 12:37:55	2025-06-29 12:37:55
23	بانک سپه	sepah	015	/assets/images/banks/sepah.svg	["589210"]	#0093dd	t	2025-06-29 12:37:55	2025-06-29 12:37:55
24	بانک شهر	shahr	061	/assets/images/banks/shahr.svg	["502806","504706"]	#d00	t	2025-06-29 12:37:55	2025-06-29 12:37:55
25	بانک سینا	sina	059	/assets/images/banks/sina.svg	["639346"]	#16469c	t	2025-06-29 12:37:55	2025-06-29 12:37:55
26	بانک حکمت ایرانیان	hekmat	065	/assets/images/banks/hekmat.svg	["636949"]	#0057a1	t	2025-06-29 12:37:55	2025-06-29 12:37:55
27	بانک توسعه صادرات	tosee_saderat	020	/assets/images/banks/tosesaderat.svg	["627648","207177"]	#066e16	t	2025-06-29 12:37:55	2025-06-29 12:37:55
28	بانک توسعه تعاون	tosee_taavon	022	/assets/images/banks/tosetaavon.svg	["502908"]	#0b8a93	t	2025-06-29 12:37:55	2025-06-29 12:37:55
29	بانک تجارت	tejarat	018	/assets/images/banks/tejarat.svg	["627353","585983"]	#1f0d8a	t	2025-06-29 12:37:55	2025-06-29 12:37:55
30	بانک ایران زمین	iranzamin	069	/assets/images/banks/iranzamin.svg	["505785"]	#490fa2	t	2025-06-29 12:37:55	2025-06-29 12:37:55
31	بانک خاورمیانه	khavarmianeh	080	/assets/images/banks/khavarmianeh.svg	["585947"]	#f7941e	t	2025-06-29 12:37:55	2025-06-29 12:37:55
32	بانک قرض‌ الحسنه رسالت	resalat	070	/assets/images/banks/resalat.svg	["504172"]	#0092cf	t	2025-06-29 12:37:55	2025-06-29 12:37:55
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.categories (id, name, slug, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, created_at, updated_at) FROM stdin;
10	بیمه	insurance	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-09-08 18:42:32	2025-09-08 18:42:32
11	خدمات اجتماعی	social-services	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-09-08 18:42:32	2025-09-08 18:42:32
12	شناسایی و احراز هویت	kyc	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-09-08 18:42:32	2025-09-08 18:42:32
13	دیگر خدمات	other-services	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-09-08 18:42:32	2025-09-08 18:42:32
\.


--
-- Data for Name: comments; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.comments (id, post_id, author_name, author_email, content, meta_description, created_at, updated_at, is_approved, likes_count, parent_id) FROM stdin;
\.


--
-- Data for Name: contact_messages; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.contact_messages (id, name, email, subject, message, is_read, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: currencies; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.currencies (id, code, name, symbol, exchange_rate, is_base_currency, is_active, decimal_places, "position", created_at, updated_at) FROM stdin;
1	IRT	Iranian Toman	تومان	1.00000000	t	t	0	after	2025-07-09 06:15:46	2025-07-09 06:15:46
2	USD	US Dollar	$	0.00002400	f	t	2	before	2025-07-09 06:15:46	2025-07-09 06:15:46
3	EUR	Euro	€	0.00002200	f	t	2	before	2025-07-09 06:15:46	2025-07-09 06:15:46
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: filament_filter_set_user; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.filament_filter_set_user (id, user_id, filter_set_id, sort_order, is_visible) FROM stdin;
\.


--
-- Data for Name: filament_filter_sets; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.filament_filter_sets (id, user_id, name, resource, filters, indicators, is_public, is_global_favorite, sort_order, created_at, updated_at, color, icon, status, tenant_id) FROM stdin;
\.


--
-- Data for Name: filament_filter_sets_managed_preset_views; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.filament_filter_sets_managed_preset_views (id, user_id, name, label, resource, is_favorite, sort_order, created_at, updated_at, tenant_id) FROM stdin;
\.


--
-- Data for Name: footer_contents; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.footer_contents (id, key, value, type, section, is_active, settings, created_at, updated_at) FROM stdin;
2	company_description	پیشخوانک، پلتفرم جامع خدمات مالی آنلاین است که با بهره‌گیری از جدیدترین تکنولوژی‌ها، دسترسی آسان و سریع به انواع خدمات بانکی، پرداخت، بیمه و سرمایه‌گذاری را فراهم می‌کند.	text	general	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
3	copyright	© 1403 پیشخوانک. تمامی حقوق محفوظ است.	text	legal	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
4	terms_privacy	استفاده از این سایت به منزله پذیرش <a href="/terms">قوانین و مقررات</a> و <a href="/privacy">حریم خصوصی</a> می‌باشد.	html	legal	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
5	company_name	پیشخوانک	text	general	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
6	company_address	تهران، خیابان ولیعصر، پلاک 123	text	contact	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
7	company_phone	021-12345678	text	contact	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
8	company_email	info@pishkhanak.com	text	contact	t	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
\.


--
-- Data for Name: footer_links; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.footer_links (id, footer_section_id, title, url, icon, sort_order, is_active, open_in_new_tab, target, attributes, created_at, updated_at) FROM stdin;
1	2	خدمات بانکی	/services/banking-services	\N	1	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
2	2	درگاه‌های پرداخت	/services/payment-gateways	\N	2	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
3	2	ارزهای دیجیتال	/services/cryptocurrency	\N	3	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
4	2	بیمه	/services/insurance	\N	4	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
5	2	سرمایه‌گذاری	/services/investment	\N	5	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
6	3	تماس با ما	/contact	\N	6	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
7	3	سوالات متداول	/faq	\N	7	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
8	3	راهنمای استفاده	/help	\N	8	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
9	3	گزارش مشکل	/report	\N	9	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
10	3	درخواست پشتیبانی	/support	\N	10	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
11	4	صفحه اصلی	/	\N	11	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
12	4	درباره ما	/about	\N	12	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
13	4	خدمات	/services	\N	13	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
14	4	وبلاگ	/blog	\N	14	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
15	4	اخبار	/news	\N	15	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
16	5	تهران، خیابان ولیعصر	#	location	16	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
17	5	021-12345678	tel:02112345678	phone	17	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
18	5	info@pishkhanak.com	mailto:info@pishkhanak.com	email	18	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
19	5	@pishkhanak	https://telegram.me/pishkhanak	telegram	19	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
20	5	پیشخوانک	https://instagram.com/pishkhanak	instagram	20	t	f	_self	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
21	2	خدمات بانکی	/services/banking-services	\N	1	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
22	2	درگاه‌های پرداخت	/services/payment-gateways	\N	2	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
23	2	ارزهای دیجیتال	/services/cryptocurrency	\N	3	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
24	2	بیمه	/services/insurance	\N	4	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
25	2	سرمایه‌گذاری	/services/investment	\N	5	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
26	3	تماس با ما	/contact	\N	6	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
27	3	سوالات متداول	/faq	\N	7	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
28	3	راهنمای استفاده	/help	\N	8	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
29	3	گزارش مشکل	/report	\N	9	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
30	3	درخواست پشتیبانی	/support	\N	10	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
31	4	صفحه اصلی	/	\N	11	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
32	4	درباره ما	/about	\N	12	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
33	4	خدمات	/services	\N	13	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
34	4	وبلاگ	/blog	\N	14	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
35	4	اخبار	/news	\N	15	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
36	5	تهران، خیابان ولیعصر	#	location	16	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
37	5	021-12345678	tel:02112345678	phone	17	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
38	5	info@pishkhanak.com	mailto:info@pishkhanak.com	email	18	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
39	5	@pishkhanak	https://telegram.me/pishkhanak	telegram	19	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
40	5	پیشخوانک	https://instagram.com/pishkhanak	instagram	20	t	f	_self	\N	2025-09-08 16:33:20	2025-09-08 16:33:20
41	7	کارت به شبا	/services/card-iban	\N	1	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
42	7	کارت به حساب	/services/card-account	\N	2	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
43	7	شبا به حساب	/services/iban-account	\N	3	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
44	7	حساب به شبا	/services/account-iban	\N	4	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
45	7	بررسی شبا	/services/iban-check	\N	5	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
46	8	استعلام چک برگشتی	/services/check-inquiry	\N	1	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
47	8	استعلام مکنا	/services/mekna-inquiry	\N	2	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
48	8	وضعیت رنگ چک	/services/check-color	\N	3	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
49	8	اعتبارسنجی بانکی	/services/bank-validation	\N	4	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
50	9	استعلام خلافی	/services/traffic-violation	\N	1	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
51	9	استعلام بیمه	/services/insurance-inquiry	\N	2	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
52	9	استعلام معاینه فنی	/services/technical-inspection	\N	3	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
53	9	استعلام پلاک	/services/plate-inquiry	\N	4	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
54	10	استعلام کدپستی	/services/postal-code	\N	1	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
55	10	استعلام کد ملی	/services/national-id	\N	2	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
56	10	استعلام نظام وظیفه	/services/military-service	\N	3	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
57	10	استعلام تلفن	/services/phone-inquiry	\N	4	t	f	_self	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
\.


--
-- Data for Name: footer_sections; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.footer_sections (id, title, slug, description, icon, sort_order, is_active, location, settings, created_at, updated_at) FROM stdin;
2	خدمات	services	\N	\N	1	t	footer	\N	2025-09-08 16:32:33	2025-09-08 16:32:33
3	پشتیبانی	support	\N	\N	2	t	footer	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
4	دسترسی سریع	quick-access	\N	\N	3	t	footer	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
5	تماس با ما	contact	\N	\N	4	t	footer	\N	2025-09-08 16:32:34	2025-09-08 16:32:34
7	خدمات بانکی	banking-services	انواع خدمات بانکی و مالی	heroicon-o-credit-card	1	t	footer	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
8	خدمات چک	check-services	استعلام و بررسی چک	heroicon-o-document-text	2	t	footer	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
9	خدمات خودرو	vehicle-services	استعلام خودرو و خلافی	heroicon-o-truck	3	t	footer	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
10	سایر خدمات	other-services	سایر خدمات استعلامی	heroicon-o-cog	4	t	footer	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
\.


--
-- Data for Name: gateway_transaction_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.gateway_transaction_logs (id, gateway_transaction_id, action, source, message, data, request_data, response_data, ip_address, user_agent, method, url, headers, error_code, error_message, stack_trace, response_time_ms, memory_usage_mb, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: gateway_transactions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.gateway_transactions (id, uuid, user_id, payment_gateway_id, currency_id, amount, tax_amount, gateway_fee, total_amount, gateway_transaction_id, gateway_reference, gateway_response, type, status, description, metadata, user_ip, user_agent, user_country, user_device, processed_at, completed_at, failed_at, expired_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: media; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.media (id, model_type, model_id, uuid, collection_name, name, file_name, mime_type, disk, conversions_disk, size, manipulations, custom_properties, generated_conversions, responsive_images, order_column, created_at, updated_at) FROM stdin;
1	App\\\\Models\\\\Service	6	7a23f252-9f0d-4a5b-8fd3-233b6be33e86	default	credit-score-rating	01JZD3RQQ3ZJE0N37MFNFQT2GB.webp	image/webp	thumbnails	thumbnails	18454	[]	[]	[]	[]	1	2025-07-05 10:57:59	2025-07-05 10:57:59
10	App\\\\Models\\\\Service	6	dba027b5-2b5d-4926-9b6e-8c2ee97ab622	icon	007-credit-score-1	01JZJ8W9QYDP46FN57JHN3P4AD.svg	image/svg+xml	thumbnails	thumbnails	10776	[]	[]	[]	[]	3	2025-07-07 11:03:30	2025-07-07 11:03:30
12	App\\\\Models\\\\Service	4	5c5abf42-8a4d-4807-8ba9-f566ad0fb649	icon	iban-account	01JZK5TV2RG0PEHGC66STXZBC5.svg	image/svg+xml	thumbnails	thumbnails	6242	[]	[]	[]	[]	1	2025-07-07 19:29:31	2025-07-07 19:29:31
13	App\\\\Models\\\\Service	3	706db2aa-958b-426b-8d72-c468391f492a	icon	account-iban	01JZK5VXRGKZRS9TZQ2HZ7NRE1.svg	image/svg+xml	thumbnails	thumbnails	6186	[]	[]	[]	[]	1	2025-07-07 19:30:07	2025-07-07 19:30:07
14	App\\\\Models\\\\Service	2	b45ffb7b-a010-4ddb-9f0b-df93adb59c67	icon	card-account	01JZK5WDBGBG207VD2W9R6N5N9.svg	image/svg+xml	thumbnails	thumbnails	9112	[]	[]	[]	[]	2	2025-07-07 19:30:23	2025-07-07 19:30:23
15	App\\\\Models\\\\Service	1	d5f9d99d-801a-4e83-bf35-ff9c2ab24b07	icon	card-iban	01JZK5WVNN5A3RFRBGA1G9NGBN.svg	image/svg+xml	thumbnails	thumbnails	9099	[]	[]	[]	[]	2	2025-07-07 19:30:37	2025-07-07 19:30:37
16	App\\\\Models\\\\Service	5	07421984-dfc0-4241-9805-5b13649dba9f	icon	iban-check	01JZK66YGWFW6WMPCNEJTY375D.svg	image/svg+xml	thumbnails	thumbnails	9180	[]	[]	[]	[]	1	2025-07-07 19:36:08	2025-07-07 19:36:08
17	App\\\\Models\\\\Service	6	cc806a14-0335-427d-a9f8-d8512d16644f	thumbnail	credit-score-rating	01JZM41EY55AXQAF6TZWK6ZGP9.webp	image/webp	thumbnails	thumbnails	22270	[]	[]	[]	[]	4	2025-07-08 04:17:25	2025-07-08 04:17:25
18	App\\\\Models\\\\Service	5	b4a35536-da3a-40d9-a311-199e05383f2a	thumbnail	iban-check	01JZM4222J5HJ6NDYNPERHSFBT.webp	image/webp	thumbnails	thumbnails	16442	[]	[]	[]	[]	2	2025-07-08 04:17:45	2025-07-08 04:17:45
19	App\\\\Models\\\\Service	4	d6829a6e-292d-4b10-8034-efa47bd9efc9	thumbnail	iban-account	01JZM42FWVB8AF11VXCB468EGT.webp	image/webp	thumbnails	thumbnails	23324	[]	[]	[]	[]	2	2025-07-08 04:17:59	2025-07-08 04:17:59
21	App\\\\Models\\\\Service	2	a49341aa-829a-4e21-b88f-b5687ff4706b	thumbnail	card-account	01JZM4A65P8YSHM9K3KH5BJE63.webp	image/webp	thumbnails	thumbnails	25308	[]	[]	[]	[]	3	2025-07-08 04:22:11	2025-07-08 04:22:11
22	App\\\\Models\\\\Service	1	ab959ad7-5b0e-474a-b8e5-78d8dda74dfe	thumbnail	card-iban	01JZM4ARMHX68P7MS5DQHYDH56.webp	image/webp	thumbnails	thumbnails	24412	[]	[]	[]	[]	3	2025-07-08 04:22:30	2025-07-08 04:22:30
23	App\\\\Models\\\\Service	3	4c3fb08c-6ad8-4dc8-92b4-30bcc01a376b	thumbnail	account-iban	01JZM4DMTRN0Z7B7CNRBXWR68C.webp	image/webp	thumbnails	thumbnails	23482	[]	[]	[]	[]	2	2025-07-08 04:24:05	2025-07-08 04:24:05
24	App\\\\Models\\\\Service	30	e0c1e2aa-19be-4b28-ad2f-ca7d6787ea13	icon	driver	01JZZ5HWXDSCDPMFKMZXVEWSRX.svg	image/svg+xml	thumbnails	thumbnails	14739	[]	[]	[]	[]	1	2025-07-12 11:15:31	2025-07-12 11:15:31
25	App\\\\Models\\\\Service	29	191c280e-32e9-45a4-8974-ea7fc46aa069	icon	car-insurance	01JZZ5QYT3S9JV3J59D60SAJGX.png	image/png	thumbnails	thumbnails	2823	[]	[]	[]	[]	1	2025-07-12 11:18:50	2025-07-12 11:18:50
26	App\\\\Models\\\\Service	28	de5592d2-ebb7-4ef6-be85-36ef0580dc02	icon	license-plate	01JZZ62BZHV8N68YN7HN0SP8S7.png	image/png	thumbnails	thumbnails	2552	[]	[]	[]	[]	1	2025-07-12 11:24:31	2025-07-12 11:24:31
27	App\\\\Models\\\\Service	27	0def974d-f09f-42fe-8dd7-68954a8b16b0	icon	driving-license	01JZZ67900350M3Y50X2N1VQTW.png	image/png	thumbnails	thumbnails	5106	[]	[]	[]	[]	1	2025-07-12 11:27:12	2025-07-12 11:27:12
28	App\\\\Models\\\\Service	26	1fdf9602-7759-4ef9-ae28-a56ad77a04db	icon	car	01JZZ69YFNG972QTK4CB4DF9J9.svg	image/svg+xml	thumbnails	thumbnails	3560	[]	[]	[]	[]	1	2025-07-12 11:28:39	2025-07-12 11:28:39
29	App\\\\Models\\\\Service	25	fd57aa1a-1cf1-42aa-95bc-ed187b7b281d	icon	toll	01JZZ6C8XV7AGFTA2FN8HZTKQR.svg	image/svg+xml	thumbnails	thumbnails	5434	[]	[]	[]	[]	1	2025-07-12 11:29:56	2025-07-12 11:29:56
30	App\\\\Models\\\\Service	24	cc8c11ae-f2ed-46a9-bf7d-f8542273bb25	icon	cars	01JZZ6GRYTQ9VYEMNTTBYT0H4M.png	image/png	thumbnails	thumbnails	7293	[]	[]	[]	[]	1	2025-07-12 11:32:23	2025-07-12 11:32:23
31	App\\\\Models\\\\Service	23	d3f4e85d-61b6-40b3-ae7f-603df8352443	icon	license	01JZZ6PEWYJYQAJ1ET3B27WA27.svg	image/svg+xml	thumbnails	thumbnails	7097	[]	[]	[]	[]	1	2025-07-12 11:35:29	2025-07-12 11:35:29
32	App\\\\Models\\\\Service	21	8731a462-f3b7-4b8b-a1b4-e3333200134b	icon	cancel	01JZZ6Z7BB26Q2W1BBBHRG8SWY.svg	image/svg+xml	thumbnails	thumbnails	2756	[]	[]	[]	[]	1	2025-07-12 11:40:17	2025-07-12 11:40:17
33	App\\\\Models\\\\Service	20	4fbfc850-5d25-4390-8d41-dda7abbad684	icon	enforcement	01JZZ71CKGGBWHKA7K03A1F47C.svg	image/svg+xml	thumbnails	thumbnails	3742	[]	[]	[]	[]	1	2025-07-12 11:41:28	2025-07-12 11:41:28
34	App\\\\Models\\\\Service	19	c36737bd-6091-4e46-87c8-114d535bb506	icon	motorcycle	01JZZ75SMJ4SYMR3QS3T28GVAJ.png	image/png	thumbnails	thumbnails	7648	[]	[]	[]	[]	1	2025-07-12 11:43:52	2025-07-12 11:43:52
35	App\\\\Models\\\\Service	18	07f1e818-f0a1-4e91-8d53-9e361cd03399	icon	transport (1)	01JZZ77QYW6SCE10C0NTZW42TA.png	image/png	thumbnails	thumbnails	3475	[]	[]	[]	[]	1	2025-07-12 11:44:56	2025-07-12 11:44:56
36	App\\\\Models\\\\Service	22	df8b7877-1f0c-40fa-8aed-78ad5ca39eb5	icon	fleet	01JZZ78N6XJYWS03221PFGTPM0.png	image/png	thumbnails	thumbnails	5932	[]	[]	[]	[]	1	2025-07-12 11:45:26	2025-07-12 11:45:26
37	App\\\\Models\\\\ServiceCategory	2	5b3d5e49-1b44-4e9f-8061-892bb734ac3b	background_image	fleet	01K0141WXF6PNRKVJKHRSPT00R.svg	image/svg+xml	thumbnails	thumbnails	9800	[]	[]	[]	[]	1	2025-07-13 05:27:47	2025-07-13 05:27:47
39	App\\\\Models\\\\ServiceCategory	1	45ff077b-1e2c-4747-b43c-406ef5aeca47	background_image	banking	01K01FKQ1PHC92A7WD5NTAJPDE.svg	image/svg+xml	thumbnails	thumbnails	9242	[]	[]	[]	[]	1	2025-07-13 08:49:46	2025-07-13 08:49:46
40	App\\\\Models\\\\ServiceCategory	3	ab8c4bbf-16a7-41c0-9fa7-2977ee390198	background_image	kyc	01K01FNJKKGR6ZABTME2P9AP6T.svg	image/svg+xml	thumbnails	thumbnails	8921	[]	[]	[]	[]	1	2025-07-13 08:50:47	2025-07-13 08:50:47
41	App\\\\Models\\\\Service	7	a2dcda14-8403-4ba3-b15a-5c5ba9471a86	icon	curriculum-vitae	01K01G2MJR3VN8W1YMQKDTW71H.svg	image/svg+xml	thumbnails	thumbnails	3628	[]	[]	[]	[]	1	2025-07-13 08:57:55	2025-07-13 08:57:55
42	App\\\\Models\\\\Service	10	b49e9633-b4fc-45af-9692-a15b19cff1da	icon	cheque (6)	01K01GDSVWTA7TPG7JQ0QMTZR2.svg	image/svg+xml	thumbnails	thumbnails	15519	[]	[]	[]	[]	1	2025-07-13 09:04:00	2025-07-13 09:04:00
43	App\\\\Models\\\\Service	11	79316093-e7b6-4570-8034-672a93288906	icon	cheque-color	01K01GRBA19YDNBMXF2MXXATXD.svg	image/svg+xml	thumbnails	thumbnails	12754	[]	[]	[]	[]	1	2025-07-13 09:09:46	2025-07-13 09:09:46
44	App\\\\Models\\\\Service	13	41dfe533-b4d1-45ff-9edd-eb46eacd20e7	icon	social-credit-system	01K01GVCCCTFSD0A34AYE04ZZT.svg	image/svg+xml	thumbnails	thumbnails	4593	[]	[]	[]	[]	1	2025-07-13 09:11:25	2025-07-13 09:11:25
46	App\\\\Models\\\\Service	15	c2b927fc-7a1e-45a6-af8d-21a79b5c9feb	icon	loans	01K01H8XS7GSNPPGWN834HXEVY.svg	image/svg+xml	thumbnails	thumbnails	13951	[]	[]	[]	[]	1	2025-07-13 09:18:49	2025-07-13 09:18:49
47	App\\\\Models\\\\Service	16	5f593c55-5294-4b5e-9c2f-f65a84c7f89f	icon	loan-approved	01K01J30R0M7YNPFED9BHQ2SW4.svg	image/svg+xml	thumbnails	thumbnails	9157	[]	[]	[]	[]	1	2025-07-13 09:33:04	2025-07-13 09:33:04
48	App\\\\Models\\\\Service	14	d39116fc-47a3-4f6c-b7bc-886605964446	icon	paycheck	01K01J9GD4T3HVKXR03GB8TSTQ.png	image/png	thumbnails	thumbnails	11563	[]	[]	[]	[]	1	2025-07-13 09:36:37	2025-07-13 09:36:37
49	App\\\\Models\\\\Service	17	684bf5bd-9b66-45b1-a5ca-f99e7e79f022	icon	kyc	01K01JG3BDHKWGX1EPETJJ3RZ1.svg	image/svg+xml	thumbnails	thumbnails	12349	[]	[]	[]	[]	1	2025-07-13 09:40:13	2025-07-13 09:40:13
50	App\\Models\\Service	1	31e62cc0-e2d8-447e-b6d8-4b552055a4ca	icon	card-iban	01K4N3GW18AENH8F0WDB0S6AAR.svg	image/svg+xml	thumbnails	thumbnails	9099	[]	[]	[]	[]	1	2025-09-08 20:16:05	2025-09-08 20:16:05
51	App\\Models\\Service	2	891a7564-8986-47b7-8c9d-35591768e5a2	icon	card-account	01K4N3MJK5A177APYQJP33DATQ.svg	image/svg+xml	thumbnails	thumbnails	9112	[]	[]	[]	[]	1	2025-09-08 20:18:07	2025-09-08 20:18:07
52	App\\Models\\Service	3	c3edb724-1623-4b32-bb1d-1d111a26b031	icon	account-iban	01K4N3P80VP27QWX17X75P2ZH8.svg	image/svg+xml	thumbnails	thumbnails	6255	[]	[]	[]	[]	1	2025-09-08 20:19:01	2025-09-08 20:19:01
53	App\\Models\\Service	4	819730b4-eac0-458c-8f14-7ea3a1115d30	icon	iban-account	01K4N3Q6C0228YP0WRT9A0CEVR.svg	image/svg+xml	thumbnails	thumbnails	6242	[]	[]	[]	[]	1	2025-09-08 20:19:33	2025-09-08 20:19:33
54	App\\Models\\Service	5	9b6a7ac6-e215-46b1-86f3-aa280af1ee1b	icon	iban-check	01K4N3QQ1C29ZKKY905SDNP3EC.svg	image/svg+xml	thumbnails	thumbnails	9180	[]	[]	[]	[]	1	2025-09-08 20:19:50	2025-09-08 20:19:50
55	App\\Models\\Service	6	7f8eda8f-8314-4dca-9c31-d9a1c3c0d633	icon	007-credit-score-1	01K4N3STV0KMCJ0ZVSXH88940D.svg	image/svg+xml	thumbnails	thumbnails	10776	[]	[]	[]	[]	1	2025-09-08 20:20:59	2025-09-08 20:20:59
56	App\\Models\\Service	7	2d944f9a-8fe1-43e2-b988-a91f78f228e5	icon	sticky-notes	01K4N3YTY43BD0EH79MS1SF9YK.svg	image/svg+xml	thumbnails	thumbnails	1567	[]	[]	[]	[]	1	2025-09-08 20:23:43	2025-09-08 20:23:43
57	App\\Models\\Service	10	808ac6fc-705e-430c-a45f-60d08286c06d	icon	cheque (6)	01K4N4302FS1RDF28J6321CDTZ.svg	image/svg+xml	thumbnails	thumbnails	15519	[]	[]	[]	[]	1	2025-09-08 20:25:59	2025-09-08 20:25:59
58	App\\Models\\Service	11	c8740da7-4498-4feb-925f-6f361f9afb3a	icon	cheque-color	01K4N45E73TAPMDKYFAHQWZRTX.svg	image/svg+xml	thumbnails	thumbnails	12754	[]	[]	[]	[]	1	2025-09-08 20:27:19	2025-09-08 20:27:19
\.


--
-- Data for Name: meta; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.meta (id, metable_type, metable_id, type, key, value, numeric_value, hmac) FROM stdin;
\.


--
-- Data for Name: metas; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.metas (id, metable_type, metable_id, key, value, type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2017_01_01_000000_create_meta_table	1
5	2018_11_06_222923_create_transactions_table	1
6	2018_11_07_192923_create_transfers_table	1
7	2018_11_15_124230_create_wallets_table	1
8	2020_01_24_000000_modify_meta_indexes	1
9	2021_11_02_202021_update_wallets_uuid_table	1
10	2023_12_30_113122_extra_columns_removed	1
11	2023_12_30_204610_soft_delete	1
12	2024_01_01_000000_create_ai_search_logs_table	1
13	2024_01_12_000000_create_categories_table	2
14	2024_01_13_000000_create_service_categories_table	2
15	2024_01_14_000000_create_services_table	2
16	2024_01_15_000000_add_pricing_fields_to_services_table	2
17	2024_01_15_000000_create_settings_table	2
18	2024_01_15_000001_create_service_requests_table	2
19	2024_01_16_000000_add_hidden_fields_to_services_table	2
20	2024_01_16_000000_create_tickets_table	2
21	2024_01_16_000001_add_short_title_to_services_table	2
22	2024_01_24_185401_add_extra_column_in_transfer	2
23	2024_04_14_000000_add_meta_search_columns	2
24	2024_04_27_000000_create_ai_contents_table	2
25	2024_04_27_000001_create_ai_settings_table	2
26	2024_07_09_000001_add_request_hash_to_service_requests_table	2
27	2024_07_19_120000_create_enhanced_ticketing_system	2
28	2024_07_22_000000_create_banks_table	2
29	2024_07_30_120622_create_personal_access_tokens_table	2
30	2024_07_30_121120_create_permission_tables	2
31	2024_07_31_072814_add_social_login_fields_to_users_table	2
32	2024_09_23_094830_create_posts_table	2
33	2024_09_23_094840_create_comments_table	2
34	2024_09_23_094840_create_pages_table	2
35	2024_09_23_124428_create_filament_filter_sets_table	2
36	2024_09_23_124429_create_filament_filter_set_user_table	2
37	2024_09_23_124430_add_icon_and_color_columns_to_filter_sets_table	2
38	2024_09_23_124431_add_is_visible_column_to_filter_set_users_table	2
39	2024_09_23_124432_create_filament_filter_sets_managed_preset_views_table	2
40	2024_09_23_124433_add_status_column_to_filter_sets_table	2
41	2024_09_23_124434_change_filter_json_column_type_to_text_type	2
42	2024_09_23_124435_add_tenant_id_to_filter_sets_table	2
43	2024_09_23_124436_add_tenant_id_to_managed_preset_views_table	2
44	2024_09_23_212844_create_tag_tables	2
45	2024_10_01_080008_create_media_table	2
46	2024_12_19_000000_create_service_results_table	2
47	2024_12_20_000000_create_tokens_table	2
48	2024_12_20_000001_migrate_legacy_tokens	2
49	2024_12_20_120000_add_wallet_transaction_id_to_service_requests_table	2
50	2024_12_20_120001_add_wallet_transaction_id_to_service_results_table	2
51	2024_12_21_000000_create_otps_table	2
52	2024_12_21_000001_add_mobile_to_users_table	2
53	2024_12_24_update_comments_table	2
54	2025_01_05_000000_add_ai_summary_to_ai_contents_table	2
55	2025_01_05_000001_add_ai_thumbnails_to_ai_contents_table	2
56	2025_01_14_000001_add_styling_fields_to_service_categories_table	2
57	2025_01_15_000000_create_metas_table	2
58	2025_01_17_000000_update_services_slug_unique_constraint	2
59	2025_01_18_add_user_id_to_service_results_table	2
60	2025_01_19_070000_create_token_refresh_logs_table	2
61	2025_01_19_120000_create_auto_response_system	2
62	2025_01_19_130000_add_auto_response_field_to_ticket_messages	2
63	2025_01_19_fix_service_requests_input_data_nullable	2
64	2025_01_21_000000_add_explanation_to_services_table	2
65	2025_01_27_000000_create_redirects_table	2
66	2025_06_24_115913_create_contact_messages_table	2
67	2025_07_02_000000_make_email_nullable_in_users_table	2
68	2025_07_02_100000_create_currencies_table	2
69	2025_07_02_100001_create_payment_gateways_table	2
70	2025_07_02_100002_create_tax_rules_table	2
71	2025_07_02_100003_create_gateway_transactions_table	2
72	2025_07_02_100004_create_gateway_transaction_logs_table	2
73	2025_07_02_100005_create_payment_methods_table	2
74	2025_07_09_120000_make_user_id_nullable_in_gateway_transactions_table	2
75	2025_07_13_051751_add_background_image_to_service_categories_table	2
76	2025_07_13_084444_add_is_active_to_services_table	2
77	2025_07_13_084548_add_keywords_to_services_table	2
78	2025_07_19_104138_add_hash_to_tickets_table	2
79	2025_07_19_213101_create_footer_sections_table	3
80	2025_07_19_213106_create_footer_links_table	3
81	2025_07_19_213111_create_site_links_table	3
82	2025_07_19_213116_create_footer_contents_table	3
83	2025_08_09_052552_add_model_relationship_to_ai_contents_table	3
84	2025_09_08_150848_create_telegram_tickets_table	4
85	2025_09_08_160000_create_telegram_admin_system_tables	4
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
1	App\\Models\\User	5
\.


--
-- Data for Name: otps; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.otps (id, mobile, code, type, expires_at, verified_at, is_used, ip_address, user_agent, attempts, last_attempt_at, created_at, updated_at) FROM stdin;
1	989153887809	78022	register	2025-07-01 19:02:45	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 18:59:45	2025-07-01 18:59:45
2	989153887809	98972	register	2025-07-01 19:07:23	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	1	2025-07-01 19:05:09	2025-07-01 19:04:23	2025-07-01 19:05:09
3	989153887809	99083	register	2025-07-01 19:16:52	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 19:13:52	2025-07-01 19:13:52
4	989153887809	79690	register	2025-07-01 20:02:29	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 19:59:29	2025-07-01 19:59:29
5	989153887809	43066	register	2025-07-01 20:33:22	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:30:22	2025-07-01 20:30:22
6	989153887809	31443	register	2025-07-01 20:37:07	\N	t	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:34:07	2025-07-01 20:36:39
7	989112697701	25727	register	2025-07-01 20:38:03	\N	t	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:35:03	2025-07-01 20:37:33
9	989112697701	95879	register	2025-07-01 20:40:33	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:37:33	2025-07-01 20:37:33
8	989153887809	37506	register	2025-07-01 20:39:39	\N	t	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:36:39	2025-07-01 20:39:38
10	989153887809	35468	register	2025-07-01 20:42:38	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:39:38	2025-07-01 20:39:38
11	989153887809	74502	register	2025-07-01 21:00:28	\N	f	45.159.112.175	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-01 20:57:28	2025-07-01 20:57:28
35	989153887809	64798	register	2025-07-02 10:14:38	\N	f	45.159.112.175	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-02 10:11:38	2025-07-02 10:11:38
39	09153887809	67243	login	2025-07-02 19:40:45	\N	f	37.40.252.46	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36	0	\N	2025-07-02 19:37:45	2025-07-02 19:37:45
36	09153887809	18925	register	2025-07-02 10:15:59	\N	f	45.159.112.175	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	1	2025-07-02 10:13:26	2025-07-02 10:12:59	2025-07-02 10:13:26
40	09153887809	34654	login	2025-07-02 19:53:40	\N	f	37.40.252.46	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36	0	\N	2025-07-02 19:50:40	2025-07-02 19:50:40
41	09153887809	26873	login	2025-07-02 19:56:40	\N	f	37.40.252.46	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36	0	\N	2025-07-02 19:53:40	2025-07-02 19:53:40
42	09153887809	92885	login	2025-07-02 19:59:42	\N	f	37.40.252.46	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36	0	\N	2025-07-02 19:56:42	2025-07-02 19:56:42
37	09153887809	18105	register	2025-07-02 10:19:10	\N	f	45.159.112.175	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	2	2025-07-02 10:16:39	2025-07-02 10:16:10	2025-07-02 10:16:39
43	09153887809	94734	login	2025-07-02 20:10:08	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-02 20:07:08	2025-07-02 20:07:08
38	09153887809	18959	register	2025-07-02 10:25:24	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	1	2025-07-02 10:22:44	2025-07-02 10:22:24	2025-07-02 10:22:44
44	09153887809	42157	login	2025-07-02 20:15:15	\N	f	37.40.252.46	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	0	\N	2025-07-02 20:12:15	2025-07-02 20:12:15
62	09153887809	58058	login	2025-07-08 14:09:00	\N	f	45.159.112.175	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36	1	2025-07-08 14:06:51	2025-07-08 14:06:00	2025-07-08 14:06:51
63	09153887809	01497	login	2025-07-12 12:11:08	\N	f	37.40.14.214	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36	1	2025-07-12 12:08:45	2025-07-12 12:08:08	2025-07-12 12:08:45
67	09153887809	24252	login	2025-07-15 13:58:02	\N	f	109.206.254.170	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36	1	2025-07-15 13:55:13	2025-07-15 13:55:02	2025-07-15 13:55:13
64	09122239742	09466	register	2025-07-14 14:04:33	\N	f	94.101.140.210	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	1	2025-07-14 14:01:49	2025-07-14 14:01:33	2025-07-14 14:01:49
61	09153887809	47489	login	2025-07-08 07:26:01	\N	f	45.159.112.175	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36	5	2025-07-08 07:24:32	2025-07-08 07:23:01	2025-07-08 07:24:32
66	09104775864	57832	register	2025-07-15 13:38:33	\N	f	178.131.144.169	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36	1	2025-07-15 13:35:49	2025-07-15 13:35:33	2025-07-15 13:35:49
65	09104775684	94220	register	2025-07-15 10:36:44	\N	f	83.122.145.57	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36	0	\N	2025-07-15 10:33:44	2025-07-15 10:33:44
68	09334172141	97873	register	2025-09-08 17:01:00	\N	f	2.145.29.135	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 16:58:14	2025-09-08 16:58:00	2025-09-08 16:58:14
69	09334172141	21268	login	2025-09-08 17:02:09	\N	f	2.145.29.135	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 16:59:21	2025-09-08 16:59:09	2025-09-08 16:59:21
70	09153887809	57380	login	2025-09-08 17:04:31	\N	f	109.206.254.170	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	1	2025-09-08 17:01:42	2025-09-08 17:01:31	2025-09-08 17:01:42
71	09055374125	05923	register	2025-09-08 17:16:03	\N	f	5.126.191.173	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 17:13:29	2025-09-08 17:13:03	2025-09-08 17:13:29
72	09134202001	15170	register	2025-09-08 17:22:00	\N	f	31.7.113.26	Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1	1	2025-09-08 17:19:09	2025-09-08 17:19:00	2025-09-08 17:19:09
93	09026603820	73385	register	2025-09-08 20:01:47	\N	f	5.218.28.24	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 19:59:00	2025-09-08 19:58:47	2025-09-08 19:59:00
73	09126831645	64589	register	2025-09-08 17:42:51	\N	f	5.114.18.34	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	1	2025-09-08 17:40:12	2025-09-08 17:39:51	2025-09-08 17:40:12
89	09149158205	63998	register	2025-09-08 19:19:36	\N	f	5.122.137.214	Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/138.0.7204.156 Mobile/15E148 Safari/604.1	1	2025-09-08 19:17:11	2025-09-08 19:16:36	2025-09-08 19:17:11
74	09126831645	35250	login	2025-09-08 17:47:02	\N	f	5.114.18.34	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	1	2025-09-08 17:44:16	2025-09-08 17:44:02	2025-09-08 17:44:16
83	09185585339	34931	register	2025-09-08 18:38:02	\N	f	5.210.58.199	Mozilla/5.0 (iPhone; CPU iPhone OS 18_0_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0.1 Mobile/15E148 Safari/604.1	3	2025-09-08 18:35:32	2025-09-08 18:35:02	2025-09-08 18:35:32
75	09165544820	56607	register	2025-09-08 18:03:04	\N	f	5.123.48.12	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 18:00:18	2025-09-08 18:00:04	2025-09-08 18:00:18
76	09305939669	82522	register	2025-09-08 18:07:58	\N	f	5.113.206.5	Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A125F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/16.2 Chrome/92.0.4515.166 Mobile Safari/537.36	1	2025-09-08 18:05:52	2025-09-08 18:04:58	2025-09-08 18:05:52
77	09184640933	92679	register	2025-09-08 18:09:20	\N	f	192.15.178.211	Mozilla/5.0 (Windows NT 6.1; rv:109.0) Gecko/20100101 Firefox/115.0	1	2025-09-08 18:06:41	2025-09-08 18:06:20	2025-09-08 18:06:41
84	09366506298	54827	register	2025-09-08 18:52:11	\N	f	164.215.136.216	Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1	1	2025-09-08 18:49:27	2025-09-08 18:49:11	2025-09-08 18:49:27
79	09124102349	18393	login	2025-09-08 18:17:10	\N	f	37.235.21.222	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	1	2025-09-08 18:14:20	2025-09-08 18:14:10	2025-09-08 18:14:20
78	09144545838	05560	register	2025-09-08 18:15:36	\N	f	212.120.223.76	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	1	2025-09-08 18:14:29	2025-09-08 18:12:36	2025-09-08 18:14:29
80	09194551183	29182	login	2025-09-08 18:20:37	\N	f	151.235.27.170	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	0	\N	2025-09-08 18:17:37	2025-09-08 18:17:37
94	09153887809	25724	login	2025-09-08 20:07:42	\N	f	37.41.83.55	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	0	\N	2025-09-08 20:04:42	2025-09-08 20:04:42
81	09197131549	35796	login	2025-09-08 18:21:07	\N	f	5.210.78.79	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36	1	2025-09-08 18:18:17	2025-09-08 18:18:07	2025-09-08 18:18:17
85	09364968684	33416	register	2025-09-08 18:54:48	\N	f	85.158.145.66	Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3.1 Mobile/15E148 Safari/604.1	1	2025-09-08 18:52:03	2025-09-08 18:51:48	2025-09-08 18:52:03
82	09194551183	56634	login	2025-09-08 18:25:40	\N	f	151.235.27.170	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	1	2025-09-08 18:23:20	2025-09-08 18:22:40	2025-09-08 18:23:20
90	09131548330	42040	register	2025-09-08 19:23:36	\N	f	5.209.253.196	Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1	1	2025-09-08 19:20:46	2025-09-08 19:20:36	2025-09-08 19:20:46
86	09102621121	48128	register	2025-09-08 18:56:30	\N	f	178.131.150.22	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36	1	2025-09-08 18:53:44	2025-09-08 18:53:30	2025-09-08 18:53:44
87	09168600399	20927	login	2025-09-08 19:07:20	\N	f	5.209.67.175	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 19:04:49	2025-09-08 19:04:20	2025-09-08 19:04:49
91	09131548330	48703	login	2025-09-08 19:26:03	\N	f	5.209.253.196	Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1	1	2025-09-08 19:23:11	2025-09-08 19:23:03	2025-09-08 19:23:11
88	09197103415	71323	register	2025-09-08 19:14:45	\N	f	89.219.199.151	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 19:12:40	2025-09-08 19:11:45	2025-09-08 19:12:40
92	09369162157	71448	register	2025-09-08 19:41:31	\N	f	5.122.201.13	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36	1	2025-09-08 19:39:00	2025-09-08 19:38:31	2025-09-08 19:39:00
97	09121126186	47948	login	2025-09-08 20:24:12	\N	f	89.198.200.4	Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1	1	2025-09-08 20:21:26	2025-09-08 20:21:12	2025-09-08 20:21:26
95	09197302032	77462	register	2025-09-08 20:09:54	\N	f	151.238.63.187	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36	1	2025-09-08 20:07:10	2025-09-08 20:06:54	2025-09-08 20:07:10
96	09195664360	49527	register	2025-09-08 20:17:55	\N	f	5.210.116.17	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/28.0 Chrome/130.0.0.0 Mobile Safari/537.36	1	2025-09-08 20:15:25	2025-09-08 20:14:55	2025-09-08 20:15:25
98	09153442188	79930	register	2025-09-08 20:27:41	\N	f	5.209.155.10	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Mobile Safari/537.36	1	2025-09-08 20:24:54	2025-09-08 20:24:41	2025-09-08 20:24:54
\.


--
-- Data for Name: pages; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.pages (id, title, slug, content, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: payment_gateways; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.payment_gateways (id, name, slug, driver, description, is_active, is_default, config, supported_currencies, fee_percentage, fee_fixed, min_amount, max_amount, logo_url, sort_order, created_at, updated_at) FROM stdin;
4	سپهر پرداخت	sepehr	Sepehr	درگاه پرداخت سپهر	f	f	"{\\"terminal_id\\":\\"99100954\\",\\"sandbox\\":false}"	"[\\"IRT\\",\\"IRR\\"]"	0.00	0	1000	50000000	\N	1	2025-09-08 17:28:47	2025-09-08 17:29:28
1	آسان پرداخت	asanpardakht	App\\Services\\PaymentGateways\\AsanpardakhtGateway	درگاه پرداخت آسان پرداخت - ارائه‌دهنده خدمات پرداخت اینترنتی	f	f	{"merchant_id":"","username":"","password":"","sandbox":true}	["IRT"]	1.50	0	1000	500000000	/assets/images/gateways/asanpardakht.png	1	2025-09-08 16:40:52	2025-09-08 17:29:28
5	Saman Electronic Payment Gateway	sep	App\\Services\\PaymentGateways\\SepGateway	Saman Electronic Payment Gateway - Secure payment processing with advanced features	t	t	{"terminal_id":"","sandbox":true,"token_expiry_minutes":20,"refund_enabled":false,"api_version":"v4.1","timeout":30,"retry_attempts":3}	["IRT"]	0.00	0	1000	500000000	/assets/images/gateways/sep.svg	1	2025-09-08 17:29:28	2025-09-08 17:29:52
\.


--
-- Data for Name: payment_methods; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.payment_methods (id, user_id, payment_gateway_id, type, name, last_four, card_type, expiry_month, expiry_year, gateway_token, gateway_data, is_default, is_active, verified_at, last_used_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: posts; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.posts (id, title, slug, content, category_id, summary, description, thumbnail, images, status, published_at, featured, views, likes, shares, author_id, ai_title, ai_summary, ai_description, ai_thumbnail, ai_images, ai_headings, ai_sections, ai_content, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, schema, json_ld, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: redirects; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.redirects (id, from_url, to_url, status_code, is_active, is_exact_match, description, hit_count, last_hit_at, created_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	admin	web	2025-09-08 19:51:40	2025-09-08 19:51:40
\.


--
-- Data for Name: service_categories; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.service_categories (id, name, slug, created_at, updated_at, background_color, border_color, icon_color, hover_border_color, hover_background_color, background_icon, display_order, is_active, background_image) FROM stdin;
1	خدمات بانکی	banking-services	2025-09-08 16:39:54	2025-09-08 18:48:36	#dcfce7	#bbf7d0	#10b981	#4ade80	#f0fdf4	<svg height="512pt" viewBox="0 -100 512 512" width="512pt" xmlns="http://www.w3.org/2000/svg" class="animate-float h-36 w-32 absolute left-12 top-0 opacity-30 pointer-events-none"><path d="m479.644531 312.746094h-447.289062c-17.871094 0-32.355469-14.488282-32.355469-32.359375v-248.027344c0-17.871094 14.484375-32.35546875 32.355469-32.35546875h447.289062c17.871094 0 32.355469 14.48437475 32.355469 32.35546875v248.03125c0 17.867187-14.484375 32.355469-32.355469 32.355469zm0 0" fill="#e6f2ec"></path><path d="m511.996094 32.355469v248.039062c0 17.867188-14.476563 32.355469-32.347656 32.355469h-33.675782c17.867188 0 32.355469-14.488281 32.355469-32.355469v-248.039062c0-17.867188-14.488281-32.355469-32.355469-32.355469h33.675782c17.871093 0 32.347656 14.488281 32.347656 32.355469zm0 0" fill="#d5e2db"></path><path d="m400.574219 80.53125h-34.191407c-1.128906 0-2.050781-.921875-2.050781-2.050781v-34.191407c0-1.128906.921875-2.054687 2.050781-2.054687h34.191407c1.128906 0 2.050781.925781 2.050781 2.054687v34.191407c.003906 1.128906-.921875 2.050781-2.050781 2.050781zm0 0" fill="#fa0"></path><path d="m466.625 80.53125h-34.191406c-1.128906 0-2.050782-.921875-2.050782-2.050781v-34.191407c0-1.128906.921876-2.054687 2.050782-2.054687h34.191406c1.128906 0 2.050781.925781 2.050781 2.054687v34.191407c.003907 1.128906-.921875 2.050781-2.050781 2.050781zm0 0" fill="#fa0"></path><path d="m402.628906 44.289062v34.191407c0 1.132812-.925781 2.050781-2.050781 2.050781h-17.394531c1.121094 0 2.050781-.917969 2.050781-2.050781v-34.191407c0-1.136718-.925781-2.054687-2.050781-2.054687h17.394531c1.125.003906 2.050781.917969 2.050781 2.054687zm0 0" fill="#ff9500"></path><path d="m468.671875 44.289062v34.191407c0 1.132812-.914063 2.050781-2.046875 2.050781h-17.394531c1.132812 0 2.050781-.917969 2.050781-2.050781v-34.191407c0-1.136718-.917969-2.054687-2.050781-2.054687h17.394531c1.132812.003906 2.046875.917969 2.046875 2.054687zm0 0" fill="#ff9500"></path><path d="m270.054688 254.785156h-216.820313c-3.054687 0-5.527344-2.472656-5.527344-5.523437v-31.199219c0-3.050781 2.472657-5.527344 5.527344-5.527344h216.816406c3.054688 0 5.527344 2.476563 5.527344 5.527344v31.199219c0 3.050781-2.472656 5.523437-5.523437 5.523437zm0 0" fill="#00b7a0"></path><path d="m275.578125 218.058594v31.203125c0 3.050781-2.472656 5.523437-5.523437 5.523437h-26.625c3.046874 0 5.523437-2.472656 5.523437-5.523437v-31.203125c0-3.046875-2.476563-5.519532-5.523437-5.519532h26.625c3.050781-.003906 5.523437 2.46875 5.523437 5.519532zm0 0" fill="#009a8e"></path><path d="m342.503906 264.875c.972656.40625 1.980469.597656 2.972656.597656 3.023438 0 5.898438-1.789062 7.132813-4.757812.550781-1.3125 1.105469-2.6875 1.667969-4.09375 3.8125-9.460938 8.128906-20.1875 15.871094-24.082032 2.972656-1.496093 3.214843-1.613281 7.363281 6.242188 2.242187 4.25 4.78125 9.0625 9.03125 12.167969 10.015625 7.316406 19.195312 1.175781 24.679687-2.492188 3.644532-2.441406 6.160156-4 7.765625-3.460937 2.789063.9375 4.851563 2.890625 7.238281 5.148437 5.042969 4.777344 12.664063 11.992188 27.992188 6.140625 7.050781-2.691406 12.855469-7.234375 17.976562-11.246094 3.363282-2.632812 3.953126-7.488281 1.320313-10.851562-2.632813-3.359375-7.488281-3.949219-10.851563-1.316406-4.464843 3.496094-9.085937 7.113281-13.953124 8.972656-6.25 2.386719-7.4375 1.257812-11.851563-2.921875-3.089844-2.921875-6.933594-6.5625-12.960937-8.582031-8.925782-2.988282-16.058594 1.785156-21.269532 5.265625-5.679687 3.800781-6.109375 3.488281-6.96875 2.859375-1.363281-.996094-3.09375-4.269532-4.480468-6.898438-3.640626-6.902344-11.21875-21.265625-27.976563-12.839844-12.957031 6.519532-18.671875 20.714844-23.261719 32.121094-.542968 1.347656-1.070312 2.660156-1.597656 3.917969-1.640625 3.9375.21875 8.464844 4.160156 10.109375zm0 0" fill="#474f54"></path><g fill="#00b7a0"><path d="m47.707031 76.464844h71.960938c4.269531 0 7.730469-3.460938 7.730469-7.730469s-3.460938-7.730469-7.730469-7.730469h-71.960938c-4.269531 0-7.730469 3.460938-7.730469 7.730469s3.460938 7.730469 7.730469 7.730469zm0 0"></path><path d="m198.289062 76.464844c4.269532 0 7.730469-3.460938 7.730469-7.730469s-3.460937-7.730469-7.730469-7.730469h-35.980468c-4.269532 0-7.730469 3.460938-7.730469 7.730469s3.460937 7.730469 7.730469 7.730469zm0 0"></path><path d="m47.707031 111.203125h174.617188c4.269531 0 7.730469-3.457031 7.730469-7.726563 0-4.269531-3.460938-7.730468-7.730469-7.730468h-174.617188c-4.269531 0-7.730469 3.460937-7.730469 7.730468.003907 4.269532 3.460938 7.726563 7.730469 7.726563zm0 0"></path><path d="m47.707031 151.535156h14.6875c4.269531 0 7.730469-3.460937 7.730469-7.730468 0-4.269532-3.460938-7.730469-7.730469-7.730469h-14.6875c-4.269531 0-7.730469 3.460937-7.730469 7.730469.003907 4.269531 3.460938 7.730468 7.730469 7.730468zm0 0"></path><path d="m97.175781 136.074219c-4.269531 0-7.730469 3.460937-7.730469 7.730469 0 4.269531 3.460938 7.730468 7.730469 7.730468h14.6875c4.265625 0 7.726563-3.460937 7.726563-7.730468 0-4.269532-3.457032-7.730469-7.726563-7.730469zm0 0"></path><path d="m147.667969 136.074219c-4.265625 0-7.726563 3.460937-7.726563 7.730469 0 4.269531 3.457032 7.730468 7.726563 7.730468h14.6875c4.269531 0 7.730469-3.460937 7.730469-7.730468 0-4.269532-3.460938-7.730469-7.730469-7.730469zm0 0"></path><path d="m199.191406 136.074219c-4.269531 0-7.730468 3.460937-7.730468 7.730469 0 4.269531 3.460937 7.730468 7.730468 7.730468h14.6875c4.265625 0 7.726563-3.460937 7.726563-7.730468 0-4.269532-3.457031-7.730469-7.726563-7.730469zm0 0"></path><path d="m263.34375 151.535156c4.269531 0 7.730469-3.460937 7.730469-7.730468 0-4.269532-3.460938-7.730469-7.730469-7.730469h-14.6875c-4.265625 0-7.726562 3.460937-7.726562 7.730469 0 4.269531 3.457031 7.730468 7.726562 7.730468zm0 0"></path><path d="m313.839844 151.535156c4.269531 0 7.730468-3.460937 7.730468-7.730468 0-4.269532-3.460937-7.730469-7.730468-7.730469h-14.6875c-4.269532 0-7.730469 3.460937-7.730469 7.730469 0 4.269531 3.460937 7.730468 7.730469 7.730468zm0 0"></path><path d="m341.914062 143.804688c0 4.269531 3.460938 7.730468 7.730469 7.730468h14.6875c4.265625 0 7.726563-3.460937 7.726563-7.730468 0-4.269532-3.457032-7.730469-7.726563-7.730469h-14.6875c-4.269531 0-7.730469 3.460937-7.730469 7.730469zm0 0"></path><path d="m391.382812 143.804688c0 4.269531 3.457032 7.730468 7.726563 7.730468h14.6875c4.269531 0 7.730469-3.460937 7.730469-7.730468 0-4.269532-3.460938-7.730469-7.730469-7.730469h-14.6875c-4.269531 0-7.726563 3.460937-7.726563 7.730469zm0 0"></path><path d="m464.292969 151.535156c4.269531 0 7.730469-3.460937 7.730469-7.730468 0-4.269532-3.460938-7.730469-7.730469-7.730469h-14.6875c-4.269531 0-7.730469 3.460937-7.730469 7.730469 0 4.269531 3.460938 7.730468 7.730469 7.730468zm0 0"></path><path d="m472.019531 177.605469c0-4.269531-3.457031-7.730469-7.726562-7.730469h-416.585938c-4.269531 0-7.730469 3.460938-7.730469 7.730469 0 4.265625 3.460938 7.726562 7.730469 7.726562h416.585938c4.269531 0 7.726562-3.460937 7.726562-7.726562zm0 0"></path></g></svg>	1	t	\N
10	بیمه	insurance	2025-09-08 18:44:11	2025-09-08 18:48:36	#dbeafe	#bfdbfe	#10b981	#4ade80	#f0fdf4	\N	10	t	\N
11	خدمات اجتماعی	social-services	2025-09-08 18:44:11	2025-09-08 18:48:36	#d1fae5	#a7f3d0	#10b981	#4ade80	#f0fdf4	\N	11	t	\N
12	شناسایی و احراز هویت	kyc	2025-09-08 18:44:11	2025-09-08 18:48:36	#fef3c7	#fde68a	#10b981	#4ade80	#f0fdf4	\N	12	t	\N
2	خودرو و موتور	vehicle-services	2025-09-08 16:39:54	2025-09-08 18:48:36	#fee2e2	#fecaca	#ef4444	#f87171	#fef2f2	<svg id="fi_2736953" class="animate-float h-36 w-36 absolute left-12 top-0 opacity-20 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g><path d="m511.94 272.516-5.552-16.516c-3.233-4.833-7.735-8.798-13.214-11.343-46.7-21.689-121.562-21.689-121.562-21.689l-64.464-41.022c-17.225-10.962-37.219-16.784-57.636-16.784h-73.575c-17.948 0-35.649 4.179-51.703 12.205l-39.192 19.596c-12.561 6.28-26.098 10.379-40.032 12.121l-28.494 3.562-12.016 16.644v41.836c0 10.94 2.676 20.585 13.155 23.729l64.925 19.478h412.903l16.517-8.784v-31.035c0-.669-.02-1.336-.06-1.998z" fill="#ffcccb"/><path d="m493.58 305.548c-7.708 0-14.341-5.352-16.05-12.868-5.85-25.732-28.866-44.938-56.369-44.938-27.502 0-50.519 19.206-56.369 44.938-1.709 7.517-8.342 12.868-16.05 12.868h-177.226c-7.708 0-14.341-5.352-16.05-12.868-5.85-25.732-28.866-44.938-56.369-44.938-22.714 0-42.298 13.124-51.719 32.191-3.561 7.206-11.772 10.795-19.471 8.485-6.761-2.028-11.391-8.251-11.391-15.31v-60.464l-2.049.256c-8.265 1.034-14.467 8.06-14.467 16.389v1.937l6.75 12.274-6.75 12.5v22.857c0 10.94 7.176 20.586 17.655 23.729l64.925 19.478h412.903c9.122 0 16.516-7.395 16.516-16.516z" fill="#ffb3b3"/><path d="m328.887 215.355-30.605-19.477c-14.575-9.275-31.493-14.201-48.769-14.201h-18.288l-8.808 5.406-7.708-5.406h-38.772c-8.479 0-16.893 1.088-25.042 3.216l-18.466 6.846c-.27.132-.54.265-.809.4l-18.286 9.143c-3.656 1.828-4.447 6.701-1.557 9.592 1.79 1.79 3.727 3.398 5.782 4.813l197.336 7.28h11.774c4.132.001 5.703-5.394 2.218-7.612z" fill="#ff9999"/><path d="m249.512 198.194h-73.575c-15.385 0-30.557 3.582-44.317 10.462l-14.06 7.032c2.671 1.836 5.545 3.348 8.558 4.51l16.309 2.77h72.283l8.416-6.468 8.1 6.468h83.671l-16.614-10.573c-14.577-9.275-31.495-14.201-48.771-14.201z" fill="#ff8080"/><path d="m338.58 289.032h-156.903c-4.564 0-8.258-3.698-8.258-8.258s3.694-8.258 8.258-8.258h156.903c4.564 0 8.258 3.698 8.258 8.258 0 4.561-3.693 8.258-8.258 8.258z" fill="#ffe6e6"/><circle cx="99.097" cy="305.548" fill="#5d5360" r="41.29"/><circle cx="99.097" cy="305.548" fill="#dad8db" r="24.774"/><circle cx="99.097" cy="305.548" fill="#eceaec" r="8.258"/><circle cx="421.161" cy="305.548" fill="#5d5360" r="41.29"/><circle cx="421.161" cy="305.548" fill="#dad8db" r="24.774"/><circle cx="421.161" cy="305.548" fill="#eceaec" r="8.258"/><path d="m132.429 191.74-6.311 28.458c4.699 1.814 9.723 2.77 14.856 2.77h1.453l8.469-38.074c-6.333 1.658-12.551 3.953-18.467 6.846z" fill="#ff6666"/><path d="m214.709 181.677h16.516v41.29h-16.516z" fill="#ff6666"/><path d="m511.935 272.516c-.36-5.994-2.302-11.679-5.547-16.516h-10.905c-4.561 0-8.258 3.697-8.258 8.258 0 4.56 3.697 8.258 8.258 8.258z" fill="#fff0af"/><path d="m0 256h16.516c4.561 0 8.258-3.697 8.258-8.258v-8.258c0-4.561-3.697-8.258-8.258-8.258h-16.516z" fill="#ff8086"/></g></svg>	2	t	\N
\.


--
-- Data for Name: service_requests; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.service_requests (id, service_id, user_id, input_data, status, payment_transaction_id, processed_at, created_at, updated_at, request_hash, wallet_transaction_id, error_message) FROM stdin;
1	29	\N	{"plate_part1":"63","plate_letter":"\\u0637","plate_part2":"224","plate_part3":"40","national_code":"0075127733"}	guest	\N	\N	2025-09-08 16:54:06	2025-09-08 16:54:06	req_99CCDAECE605600D	\N	\N
2	29	\N	{"plate_part1":"35","plate_letter":"\\u0647","plate_part2":"571","plate_part3":"78","national_code":"0748827226"}	guest	\N	\N	2025-09-08 16:54:25	2025-09-08 16:54:25	req_48B15FFC6A2657B4	\N	\N
3	29	\N	{"plate_part1":"29","plate_letter":"\\u062f","plate_part2":"539","plate_part3":"72","national_code":"3256226711"}	guest	\N	\N	2025-09-08 16:55:13	2025-09-08 16:55:13	req_01F24D95DE46C034	\N	\N
4	29	\N	{"plate_part1":"63","plate_letter":"\\u0637","plate_part2":"224","plate_part3":"40","national_code":"0075127733"}	guest	\N	\N	2025-09-08 16:55:34	2025-09-08 16:55:34	req_0A39A5C16F33ABD6	\N	\N
5	29	\N	{"plate_part1":"89","plate_letter":"\\u062f","plate_part2":"519","plate_part3":"50","national_code":"4269564520"}	guest	\N	\N	2025-09-08 16:57:46	2025-09-08 16:57:46	req_D3680B9F36BF0501	\N	\N
6	6	\N	{"national_code":"2130206220","mobile":"09334172141"}	guest	\N	\N	2025-09-08 16:57:47	2025-09-08 16:57:47	req_3002E485514ABD1F	\N	\N
7	29	\N	{"plate_part1":"16","plate_letter":"\\u0644","plate_part2":"423","plate_part3":"54","national_code":"4420620722"}	guest	\N	\N	2025-09-08 16:58:09	2025-09-08 16:58:09	req_6B87EC101BE4337A	\N	\N
8	6	\N	{"national_code":"1980108498","mobile":"09900208260"}	guest	\N	\N	2025-09-08 16:58:13	2025-09-08 16:58:13	req_5EF0C969112E6ABA	\N	\N
9	6	8	[]	insufficient_balance	\N	\N	2025-09-08 16:58:26	2025-09-08 16:58:26	req_A9EAECACFF166B1B	\N	\N
10	29	\N	{"plate_part1":"60","plate_letter":"\\u0646","plate_part2":"852","plate_part3":"89","national_code":"0046839534"}	guest	\N	\N	2025-09-08 17:00:19	2025-09-08 17:00:19	req_3960A544091EE124	\N	\N
11	29	\N	{"plate_part1":"39","plate_letter":"\\u0637","plate_part2":"489","plate_part3":"14","national_code":"1989097383"}	guest	\N	\N	2025-09-08 17:00:32	2025-09-08 17:00:32	req_DB30B038A13B2752	\N	\N
12	29	\N	{"plate_part1":"27","plate_letter":"\\u062c","plate_part2":"972","plate_part3":"60","national_code":"0451204395"}	guest	\N	\N	2025-09-08 17:01:05	2025-09-08 17:01:05	req_4E490975C88D97F8	\N	\N
13	29	\N	{"plate_part1":"25","plate_letter":"\\u06cc","plate_part2":"666","plate_part3":"45","national_code":"3130925961"}	guest	\N	\N	2025-09-08 17:01:55	2025-09-08 17:01:55	req_B6A1D4174D57D384	\N	\N
14	29	\N	{"plate_part1":"47","plate_letter":"\\u06cc","plate_part2":"122","plate_part3":"68","national_code":"0070040291"}	guest	\N	\N	2025-09-08 17:02:29	2025-09-08 17:02:29	req_9AB2DAE582116BFC	\N	\N
15	6	\N	{"national_code":"1987655761","mobile":"09031971858"}	guest	\N	\N	2025-09-08 17:03:04	2025-09-08 17:03:04	req_A057FEF6CBA8CD12	\N	\N
16	29	\N	{"plate_part1":"89","plate_letter":"\\u0646","plate_part2":"553","plate_part3":"61","national_code":"3790329711"}	guest	\N	\N	2025-09-08 17:03:05	2025-09-08 17:03:05	req_07CC172941F9703E	\N	\N
17	29	\N	{"plate_part1":"33","plate_letter":"\\u062c","plate_part2":"871","plate_part3":"15","national_code":"1374568961"}	guest	\N	\N	2025-09-08 17:03:08	2025-09-08 17:03:08	req_E70168B0181D540F	\N	\N
18	29	\N	{"plate_part1":"45","plate_letter":"\\u06cc","plate_part2":"517","plate_part3":"43","national_code":"1100312811"}	guest	\N	\N	2025-09-08 17:03:18	2025-09-08 17:03:18	req_D8CAC05156F73A21	\N	\N
19	29	\N	{"plate_part1":"78","plate_letter":"\\u0647","plate_part2":"478","plate_part3":"36","national_code":"0926438141"}	guest	\N	\N	2025-09-08 17:03:33	2025-09-08 17:03:33	req_E1E2DC7D5AC2EBB0	\N	\N
20	29	\N	{"plate_part1":"54","plate_letter":"\\u0644","plate_part2":"291","plate_part3":"98","national_code":"4530128334"}	guest	\N	\N	2025-09-08 17:03:39	2025-09-08 17:03:39	req_565A3F80E322B7BF	\N	\N
21	6	\N	{"national_code":"0590247247","mobile":"09128559920"}	guest	\N	\N	2025-09-08 17:04:25	2025-09-08 17:04:25	req_18F206FB760CEE91	\N	\N
22	29	\N	{"plate_part1":"45","plate_letter":"\\u062f","plate_part2":"234","plate_part3":"50","national_code":"4030878032"}	guest	\N	\N	2025-09-08 17:04:30	2025-09-08 17:04:30	req_5896090A394DA359	\N	\N
23	6	\N	{"national_code":"4220591273","mobile":"09178457324"}	guest	\N	\N	2025-09-08 17:04:32	2025-09-08 17:04:32	req_72ACE73DE6AD945B	\N	\N
24	29	\N	{"plate_part1":"51","plate_letter":"\\u0648","plate_part2":"841","plate_part3":"54","national_code":"4420629746"}	guest	\N	\N	2025-09-08 17:05:18	2025-09-08 17:05:18	req_33E2433CBD810147	\N	\N
25	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:05:30	2025-09-08 17:05:30	req_AA169A26E562D17D	\N	\N
26	29	\N	{"plate_part1":"89","plate_letter":"\\u0646","plate_part2":"553","plate_part3":"61","national_code":"3790329711"}	guest	\N	\N	2025-09-08 17:05:45	2025-09-08 17:05:45	req_71D92A570ADA419A	\N	\N
27	29	\N	{"plate_part1":"36","plate_letter":"\\u0628","plate_part2":"184","plate_part3":"57","national_code":"3860372122"}	guest	\N	\N	2025-09-08 17:06:13	2025-09-08 17:06:13	req_8F7A5823744ED8A8	\N	\N
28	29	\N	{"plate_part1":"78","plate_letter":"\\u0645","plate_part2":"872","plate_part3":"79","national_code":"4324415226"}	guest	\N	\N	2025-09-08 17:06:31	2025-09-08 17:06:31	req_091F97AFC8B7655C	\N	\N
29	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:08:30	2025-09-08 17:08:30	req_A5D0140DAA9EF84B	\N	\N
30	29	\N	{"plate_part1":"49","plate_letter":"\\u0635","plate_part2":"385","plate_part3":"83","national_code":"2297924100"}	guest	\N	\N	2025-09-08 17:09:07	2025-09-08 17:09:07	req_C9A484D9C216A81E	\N	\N
31	36	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:09:18	2025-09-08 17:09:18	req_09C3650B2E317473	\N	\N
32	6	\N	{"national_code":"1730053939","mobile":"09142076830"}	guest	\N	\N	2025-09-08 17:09:47	2025-09-08 17:09:47	req_1E273324BAA1C155	\N	\N
33	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:09:56	2025-09-08 17:09:56	req_D5946781C7ED9B7D	\N	\N
34	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:10:26	2025-09-08 17:10:26	req_A053F22190EC9944	\N	\N
35	29	\N	{"plate_part1":"26","plate_letter":"\\u0628","plate_part2":"767","plate_part3":"60","national_code":"0043166911"}	guest	\N	\N	2025-09-08 17:10:46	2025-09-08 17:10:46	req_4CF78DA993AFB319	\N	\N
36	16	\N	{"national_code":"0373108915","mobile":"09332835952"}	guest	\N	\N	2025-09-08 17:11:36	2025-09-08 17:11:36	req_E5713E636EA5BC91	\N	\N
37	29	\N	{"plate_part1":"87","plate_letter":"\\u0642","plate_part2":"379","plate_part3":"32","national_code":"0919759149"}	guest	\N	\N	2025-09-08 17:11:55	2025-09-08 17:11:55	req_E516F88FB008CBD4	\N	\N
38	33	2	{"national_code":"4890062025","mobile":"09001790555"}	insufficient_balance	\N	\N	2025-09-08 17:11:55	2025-09-08 17:11:55	req_74D5774DCEDD951E	\N	\N
39	29	2	{"plate_part1":"79","plate_letter":"\\u0628","plate_part2":"555","plate_part3":"68","national_code":"4890383735"}	insufficient_balance	\N	\N	2025-09-08 17:12:52	2025-09-08 17:12:52	req_9EC1BB27098A1F0A	\N	\N
40	16	\N	{"national_code":"6449432616","mobile":"09381614381"}	guest	\N	\N	2025-09-08 17:13:26	2025-09-08 17:13:26	req_BE636E7007B019D4	\N	\N
41	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:14:33	2025-09-08 17:14:33	req_DE36F07D63640FE0	\N	\N
42	29	171	{"plate_part1":"87","plate_letter":"\\u0642","plate_part2":"379","plate_part3":"32","national_code":"0919759149"}	insufficient_balance	\N	\N	2025-09-08 17:14:54	2025-09-08 17:14:54	req_0C883C1ADD43D7E2	\N	\N
43	29	\N	{"plate_part1":"29","plate_letter":"\\u0633","plate_part2":"778","plate_part3":"21","national_code":"0420889728"}	guest	\N	\N	2025-09-08 17:15:24	2025-09-08 17:15:24	req_CE0B303AECB8A44F	\N	\N
44	6	\N	{"national_code":"3410578358","mobile":"09383333285"}	guest	\N	\N	2025-09-08 17:16:02	2025-09-08 17:16:02	req_1F580897FEBBDA4B	\N	\N
45	29	\N	{"plate_part1":"13","plate_letter":"\\u0633","plate_part2":"545","plate_part3":"74","national_code":"0924278005"}	guest	\N	\N	2025-09-08 17:17:31	2025-09-08 17:17:31	req_112901883B588686	\N	\N
46	29	172	{"plate_part1":"52","plate_letter":"\\u062c","plate_part2":"896","plate_part3":"13","national_code":"1284852830"}	insufficient_balance	\N	\N	2025-09-08 17:19:49	2025-09-08 17:19:49	req_DFFC52C781B5F085	\N	\N
47	6	\N	{"national_code":"5369658668","mobile":"09131497349"}	guest	\N	\N	2025-09-08 17:20:41	2025-09-08 17:20:41	req_EE915DD8980D6AF4	\N	\N
48	29	\N	{"plate_part1":"28","plate_letter":"\\u0635","plate_part2":"184","plate_part3":"96","national_code":"4580522591"}	guest	\N	\N	2025-09-08 17:21:16	2025-09-08 17:21:16	req_4B3864AAC0969467	\N	\N
49	29	172	{"plate_part1":"52","plate_letter":"\\u062c","plate_part2":"896","plate_part3":"13","national_code":"1272096327"}	insufficient_balance	\N	\N	2025-09-08 17:22:13	2025-09-08 17:22:13	req_3A32D9C8943C8464	\N	\N
50	29	\N	{"plate_part1":"59","plate_letter":"\\u0628","plate_part2":"371","plate_part3":"10","national_code":"0016744551"}	guest	\N	\N	2025-09-08 17:22:19	2025-09-08 17:22:19	req_B9A11F039DA28839	\N	\N
51	6	\N	{"national_code":"1290556822","mobile":"09120415085"}	guest	\N	\N	2025-09-08 17:22:20	2025-09-08 17:22:20	req_A6255E4286FB8252	\N	\N
52	29	\N	{"plate_part1":"66","plate_letter":"\\u0646","plate_part2":"518","plate_part3":"10","national_code":"0010772367"}	guest	\N	\N	2025-09-08 17:22:29	2025-09-08 17:22:29	req_80158F5C22E27A25	\N	\N
53	29	\N	{"plate_part1":"50","plate_letter":"\\u062c","plate_part2":"476","plate_part3":"87","national_code":"0074402021"}	guest	\N	\N	2025-09-08 17:22:50	2025-09-08 17:22:50	req_1B284C84B3ED8E9C	\N	\N
54	29	\N	{"plate_part1":"38","plate_letter":"\\u0648","plate_part2":"378","plate_part3":"50","national_code":"0082679339"}	guest	\N	\N	2025-09-08 17:24:47	2025-09-08 17:24:47	req_A7B5322C60052503	\N	\N
55	29	\N	{"plate_part1":"87","plate_letter":"\\u062c","plate_part2":"476","plate_part3":"50","national_code":"0074402021"}	guest	\N	\N	2025-09-08 17:26:20	2025-09-08 17:26:20	req_44E0EE7E62813CA1	\N	\N
56	6	\N	{"national_code":"0123456789","mobile":"09123456789"}	guest	\N	\N	2025-09-08 17:26:23	2025-09-08 17:26:23	req_5E3D8F307F3B4966	\N	\N
57	6	\N	{"national_code":"0924254742","mobile":"09153887809"}	guest	\N	\N	2025-09-08 17:27:12	2025-09-08 17:27:12	req_4AE9384E1B8D91F5	\N	\N
58	29	\N	{"plate_part1":"44","plate_letter":"\\u0644","plate_part2":"546","plate_part3":"50","national_code":"0386123421"}	guest	\N	\N	2025-09-08 17:27:24	2025-09-08 17:27:24	req_1BF545C81A9FD10F	\N	\N
59	6	\N	{"national_code":"0924254742","mobile":"09153887809"}	guest	\N	\N	2025-09-08 17:29:58	2025-09-08 17:29:58	req_6F4630B2266CBD99	\N	\N
60	29	\N	{"plate_part1":"72","plate_letter":"\\u0628","plate_part2":"278","plate_part3":"36","national_code":"0937832294"}	guest	\N	\N	2025-09-08 17:30:27	2025-09-08 17:30:27	req_D2D26814221A9F2F	\N	\N
61	6	\N	{"national_code":"0924254742","mobile":"09153887809"}	guest	\N	\N	2025-09-08 17:30:45	2025-09-08 17:30:45	req_6FC75BC8D89E0E4D	\N	\N
62	29	\N	{"plate_part1":"71","plate_letter":"\\u0637","plate_part2":"324","plate_part3":"31","national_code":"5989635869"}	guest	\N	\N	2025-09-08 17:31:07	2025-09-08 17:31:07	req_96570F1A6F685A6B	\N	\N
63	6	\N	{"national_code":"0924254742","mobile":"09153887809"}	guest	\N	\N	2025-09-08 17:33:44	2025-09-08 17:33:44	req_EA7C3091B05F0A72	\N	\N
64	6	\N	{"national_code":"0690023391","mobile":"09155325495"}	guest	\N	\N	2025-09-08 17:35:09	2025-09-08 17:35:09	req_C83F484DA1E1CA97	\N	\N
65	29	\N	{"plate_part1":"85","plate_letter":"\\u0637","plate_part2":"937","plate_part3":"43","national_code":"5419853922"}	guest	\N	\N	2025-09-08 17:35:53	2025-09-08 17:35:53	req_01DE2757E13192D9	\N	\N
66	6	\N	{"national_code":"0924254742","mobile":"09153887809"}	guest	\N	\N	2025-09-08 17:36:01	2025-09-08 17:36:01	req_D62848A4B685A14E	\N	\N
67	6	\N	{"national_code":"0690023391","mobile":"09155325495"}	guest	\N	\N	2025-09-08 17:36:34	2025-09-08 17:36:34	req_AA790C36673E8828	\N	\N
68	18	\N	{"plate_part1":"52","plate_letter":"\\u0635","plate_part2":"849","plate_part3":"31"}	guest	\N	\N	2025-09-08 17:37:26	2025-09-08 17:37:26	req_20C5E68C0ADEFD5B	\N	\N
69	29	\N	{"plate_part1":"31","plate_letter":"\\u0647","plate_part2":"847","plate_part3":"11","national_code":"0380696411"}	guest	\N	\N	2025-09-08 17:37:32	2025-09-08 17:37:32	req_0850025A3054279F	\N	\N
70	6	\N	{"national_code":"2708619421","mobile":"09370888372"}	guest	\N	\N	2025-09-08 17:37:40	2025-09-08 17:37:40	req_9C1E2B48A2146B61	\N	\N
71	29	\N	{"plate_part1":"98","plate_letter":"\\u062f","plate_part2":"475","plate_part3":"71","national_code":"4623543064"}	guest	\N	\N	2025-09-08 17:38:02	2025-09-08 17:38:02	req_5FB61A41C3E7FE3F	\N	\N
72	6	\N	{"national_code":"4219186573","mobile":"09126831645"}	guest	\N	\N	2025-09-08 17:38:31	2025-09-08 17:38:31	req_41ED712DDE5613C0	\N	\N
73	6	\N	{"national_code":"0690023391","mobile":"09155325495"}	guest	\N	\N	2025-09-08 17:39:12	2025-09-08 17:39:12	req_B3F189065ED6B3A2	\N	\N
74	6	\N	{"national_code":"1755319630","mobile":"09166221690"}	guest	\N	\N	2025-09-08 17:40:20	2025-09-08 17:40:20	req_F2CAFE4A655426C9	\N	\N
75	6	173	[]	insufficient_balance	\N	\N	2025-09-08 17:40:38	2025-09-08 17:40:38	req_CAF6CB295BD70870	\N	\N
76	29	\N	{"plate_part1":"17","plate_letter":"\\u062f","plate_part2":"182","plate_part3":"96","national_code":"4609689235"}	guest	\N	\N	2025-09-08 17:41:03	2025-09-08 17:41:03	req_F71AD0223E36CB92	\N	\N
77	6	\N	{"national_code":"0920895387","mobile":"09157615869"}	guest	\N	\N	2025-09-08 17:43:30	2025-09-08 17:43:30	req_79EFF172BFCC8432	\N	\N
79	15	\N	{"national_code":"1520018541","mobile":"09143238061"}	guest	\N	\N	2025-09-08 17:45:28	2025-09-08 17:45:28	req_D179BE38F4271327	\N	\N
81	29	\N	{"plate_part1":"12","plate_letter":"\\u0648","plate_part2":"773","plate_part3":"82","national_code":"0069260461"}	guest	\N	\N	2025-09-08 17:47:15	2025-09-08 17:47:15	req_B29D036ADFE9CDFD	\N	\N
82	6	\N	{"national_code":"1381660193","mobile":"09143016720"}	guest	\N	\N	2025-09-08 17:47:59	2025-09-08 17:47:59	req_A222160CC483FD4A	\N	\N
83	6	\N	{"national_code":"1463539207","mobile":"09124796379"}	guest	\N	\N	2025-09-08 17:49:51	2025-09-08 17:49:51	req_CB9BD215D2163127	\N	\N
84	29	\N	{"plate_part1":"85","plate_letter":"\\u0637","plate_part2":"937","plate_part3":"43","national_code":"5419853922"}	guest	\N	\N	2025-09-08 17:50:02	2025-09-08 17:50:02	req_511850B9A37C1318	\N	\N
85	1	\N	{"card_number":"6037697407321295","card_number_clean":"6037697407321295"}	guest	\N	\N	2025-09-08 17:51:50	2025-09-08 17:51:50	req_EE92C500C0110F0C	\N	\N
86	29	\N	{"plate_part1":"58","plate_letter":"\\u062f","plate_part2":"228","plate_part3":"94","national_code":"4690086710"}	guest	\N	\N	2025-09-08 17:52:21	2025-09-08 17:52:21	req_425971C8069DFB2F	\N	\N
87	29	\N	{"plate_part1":"91","plate_letter":"\\u062f","plate_part2":"658","plate_part3":"28","national_code":"3979426564"}	guest	\N	\N	2025-09-08 17:52:22	2025-09-08 17:52:22	req_450B9E734A0600A3	\N	\N
88	6	\N	{"national_code":"4271599271","mobile":"09227099524"}	guest	\N	\N	2025-09-08 17:54:12	2025-09-08 17:54:12	req_6E8611181463FF24	\N	\N
89	29	\N	{"plate_part1":"34","plate_letter":"\\u0648","plate_part2":"223","plate_part3":"93","national_code":"2420823613"}	guest	\N	\N	2025-09-08 17:54:15	2025-09-08 17:54:15	req_65F0AC2819B1321C	\N	\N
90	29	\N	{"plate_part1":"20","plate_letter":"\\u0644","plate_part2":"789","plate_part3":"27","national_code":"3810399469"}	guest	\N	\N	2025-09-08 17:55:06	2025-09-08 17:55:06	req_2F50229B0C1AF81F	\N	\N
91	6	\N	{"national_code":"6609561168","mobile":"09907021247"}	guest	\N	\N	2025-09-08 17:55:49	2025-09-08 17:55:49	req_2D86BB5BBC085A82	\N	\N
92	29	\N	{"plate_part1":"78","plate_letter":"\\u0637","plate_part2":"736","plate_part3":"79","national_code":"3358384497"}	guest	\N	\N	2025-09-08 17:58:57	2025-09-08 17:58:57	req_14C754A9E921292D	\N	\N
93	29	\N	{"plate_part1":"34","plate_letter":"\\u0642","plate_part2":"887","plate_part3":"40","national_code":"0012419184"}	guest	\N	\N	2025-09-08 17:59:05	2025-09-08 17:59:05	req_794F42B9DABB9C38	\N	\N
94	29	\N	{"plate_part1":"81","plate_letter":"\\u0633","plate_part2":"194","plate_part3":"40","national_code":"4060890103"}	guest	\N	\N	2025-09-08 17:59:35	2025-09-08 17:59:35	req_77C78E2205DBE45D	\N	\N
95	29	\N	{"plate_part1":"47","plate_letter":"\\u062c","plate_part2":"872","plate_part3":"15","national_code":"1741905281"}	guest	\N	\N	2025-09-08 18:01:09	2025-09-08 18:01:09	req_02D6C2B8FA67000E	\N	\N
96	16	\N	{"national_code":"1831145057","mobile":"09057847567"}	guest	\N	\N	2025-09-08 18:01:37	2025-09-08 18:01:37	req_F8032FF8D2BC995A	\N	\N
97	29	\N	{"plate_part1":"39","plate_letter":"\\u0642","plate_part2":"586","plate_part3":"60","national_code":"0020371055"}	guest	\N	\N	2025-09-08 18:01:56	2025-09-08 18:01:56	req_350955E52997296E	\N	\N
98	29	\N	{"plate_part1":"52","plate_letter":"\\u0633","plate_part2":"165","plate_part3":"21","national_code":"0750187220"}	guest	\N	\N	2025-09-08 18:02:01	2025-09-08 18:02:01	req_CADEC6B1D0A9B64F	\N	\N
99	29	\N	{"plate_part1":"51","plate_letter":"\\u0633","plate_part2":"628","plate_part3":"77","national_code":"0440044049"}	guest	\N	\N	2025-09-08 18:03:32	2025-09-08 18:03:32	req_1A72839AA46C8174	\N	\N
100	6	\N	{"national_code":"4500531548","mobile":"09902614345"}	guest	\N	\N	2025-09-08 18:03:43	2025-09-08 18:03:43	req_0B853B96A608B65F	\N	\N
101	6	\N	{"national_code":"0690887167","mobile":"09152097671"}	guest	\N	\N	2025-09-08 18:04:21	2025-09-08 18:04:21	req_BF0CF2E78BCBEB59	\N	\N
102	6	\N	{"national_code":"2930309970","mobile":"09149808538"}	guest	\N	\N	2025-09-08 18:04:37	2025-09-08 18:04:37	req_1574C13217E90874	\N	\N
103	29	\N	{"plate_part1":"95","plate_letter":"\\u0647","plate_part2":"529","plate_part3":"21","national_code":"3990084755"}	guest	\N	\N	2025-09-08 18:05:44	2025-09-08 18:05:44	req_B9A4A6D101B4180A	\N	\N
104	29	175	{"plate_part1":"52","plate_letter":"\\u0633","plate_part2":"165","plate_part3":"21","national_code":"0750187220"}	insufficient_balance	\N	\N	2025-09-08 18:06:25	2025-09-08 18:06:25	req_20218C9A1DCC6997	\N	\N
105	15	\N	{"national_code":"0690083661","mobile":"09152421002"}	guest	\N	\N	2025-09-08 18:06:53	2025-09-08 18:06:53	req_25152E46D175BAB2	\N	\N
106	29	\N	{"plate_part1":"41","plate_letter":"\\u0642","plate_part2":"785","plate_part3":"20","national_code":"4722903093"}	guest	\N	\N	2025-09-08 18:09:07	2025-09-08 18:09:07	req_FF9618CD65908D2F	\N	\N
107	29	\N	{"plate_part1":"36","plate_letter":"\\u06cc","plate_part2":"611","plate_part3":"23","national_code":"4679065745"}	guest	\N	\N	2025-09-08 18:10:32	2025-09-08 18:10:32	req_26E8795CF6A1796B	\N	\N
108	29	\N	{"plate_part1":"16","plate_letter":"\\u0644","plate_part2":"644","plate_part3":"54","national_code":"4449891252"}	guest	\N	\N	2025-09-08 18:10:37	2025-09-08 18:10:37	req_7E51D99EA731344F	\N	\N
109	6	\N	{"national_code":"5049580269","mobile":"09144545838"}	guest	\N	\N	2025-09-08 18:10:59	2025-09-08 18:10:59	req_8585F2ED6B79AD05	\N	\N
110	29	\N	{"plate_part1":"45","plate_letter":"\\u0646","plate_part2":"318","plate_part3":"42","national_code":"5730101759"}	guest	\N	\N	2025-09-08 18:11:04	2025-09-08 18:11:04	req_9103D71F47852953	\N	\N
111	6	\N	{"national_code":"0015968766","mobile":"09124102349"}	guest	\N	\N	2025-09-08 18:13:57	2025-09-08 18:13:57	req_A12F92A237B4A3C5	\N	\N
112	29	\N	{"plate_part1":"95","plate_letter":"\\u062f","plate_part2":"674","plate_part3":"60","national_code":"0550233131"}	guest	\N	\N	2025-09-08 18:14:22	2025-09-08 18:14:22	req_3C7A91333FA895B0	\N	\N
113	6	177	[]	insufficient_balance	\N	\N	2025-09-08 18:15:33	2025-09-08 18:15:33	req_9138C1298AD55203	\N	\N
114	25	\N	{"plate_part1":"98","plate_letter":"\\u0644","plate_part2":"193","plate_part3":"77"}	guest	\N	\N	2025-09-08 18:16:35	2025-09-08 18:16:35	req_FA24735878CFB002	\N	\N
115	29	\N	{"plate_part1":"48","plate_letter":"\\u062f","plate_part2":"965","plate_part3":"76","national_code":"4282161280"}	guest	\N	\N	2025-09-08 18:17:37	2025-09-08 18:17:37	req_028521D7D08C53F5	\N	\N
116	18	\N	{"plate_part1":"73","plate_letter":"\\u0628","plate_part2":"893","plate_part3":"21"}	guest	\N	\N	2025-09-08 18:17:59	2025-09-08 18:17:59	req_0C8197BE4F58A84B	\N	\N
117	6	113	[]	insufficient_balance	\N	\N	2025-09-08 18:18:36	2025-09-08 18:18:36	req_47D92F35702D7048	\N	\N
118	18	\N	{"plate_part1":"21","plate_letter":"\\u0628","plate_part2":"893","plate_part3":"73"}	guest	\N	\N	2025-09-08 18:18:45	2025-09-08 18:18:45	req_EDD4E668FE2FDC4A	\N	\N
119	18	\N	{"plate_part1":"73","plate_letter":"\\u0628","plate_part2":"893","plate_part3":"21"}	guest	\N	\N	2025-09-08 18:19:14	2025-09-08 18:19:14	req_C53FF78920C3E852	\N	\N
120	29	\N	{"plate_part1":"71","plate_letter":"\\u0637","plate_part2":"759","plate_part3":"93","national_code":"2284317235"}	guest	\N	\N	2025-09-08 18:20:25	2025-09-08 18:20:25	req_1C6F2062E9E18CC2	\N	\N
121	29	\N	{"plate_part1":"15","plate_letter":"\\u0628","plate_part2":"777","plate_part3":"57","national_code":"6189896979"}	guest	\N	\N	2025-09-08 18:20:25	2025-09-08 18:20:25	req_97BA9DE76B05CF4C	\N	\N
122	6	\N	{"national_code":"0110074378","mobile":"09396104486"}	guest	\N	\N	2025-09-08 18:20:45	2025-09-08 18:20:45	req_2344F92D381433B7	\N	\N
123	23	\N	{"mobile":"09124444328","national_code":"0889506213","plate_number":"1336244,"}	guest	\N	\N	2025-09-08 18:20:54	2025-09-08 18:20:54	req_1BEB0D267CF888B1	\N	\N
124	6	\N	{"national_code":"5980121171","mobile":"09936945781"}	guest	\N	\N	2025-09-08 18:23:11	2025-09-08 18:23:11	req_DE510B00C34E3F83	\N	\N
125	29	\N	{"plate_part1":"87","plate_letter":"\\u0644","plate_part2":"353","plate_part3":"84","national_code":"6080171384"}	guest	\N	\N	2025-09-08 18:23:20	2025-09-08 18:23:20	req_611913687ADC94BC	\N	\N
126	29	\N	{"plate_part1":"85","plate_letter":"\\u0645","plate_part2":"622","plate_part3":"18","national_code":"3241880184"}	guest	\N	\N	2025-09-08 18:24:56	2025-09-08 18:24:56	req_516AE7EB0F0CEBFE	\N	\N
127	6	109	[]	insufficient_balance	\N	\N	2025-09-08 18:25:48	2025-09-08 18:25:48	req_90A5EFF59DDB48FD	\N	\N
128	29	\N	{"plate_part1":"85","plate_letter":"\\u0645","plate_part2":"622","plate_part3":"18","national_code":"3241880184"}	guest	\N	\N	2025-09-08 18:26:18	2025-09-08 18:26:18	req_16F8C00BB81D1525	\N	\N
129	29	\N	{"plate_part1":"78","plate_letter":"\\u0633","plate_part2":"545","plate_part3":"15","national_code":"1374294357"}	guest	\N	\N	2025-09-08 18:26:47	2025-09-08 18:26:47	req_2E7A00201E3C039B	\N	\N
130	29	\N	{"plate_part1":"41","plate_letter":"\\u0645","plate_part2":"794","plate_part3":"78","national_code":"0017280559"}	guest	\N	\N	2025-09-08 18:28:00	2025-09-08 18:28:00	req_895F250B9FB41D9D	\N	\N
131	6	109	[]	insufficient_balance	\N	\N	2025-09-08 18:28:05	2025-09-08 18:28:05	req_682506306B74E874	\N	\N
132	29	\N	{"plate_part1":"65","plate_letter":"\\u0647","plate_part2":"194","plate_part3":"13","national_code":"5650075573"}	guest	\N	\N	2025-09-08 18:28:53	2025-09-08 18:28:53	req_3BBB71FAD728F668	\N	\N
133	29	\N	{"plate_part1":"78","plate_letter":"\\u0633","plate_part2":"545","plate_part3":"15","national_code":"1374294357"}	guest	\N	\N	2025-09-08 18:29:49	2025-09-08 18:29:49	req_BE630D02C0AB6CBF	\N	\N
134	29	\N	{"plate_part1":"37","plate_letter":"\\u06cc","plate_part2":"982","plate_part3":"46","national_code":"2595124307"}	guest	\N	\N	2025-09-08 18:31:28	2025-09-08 18:31:28	req_AE257BBEF8807F71	\N	\N
135	29	\N	{"plate_part1":"38","plate_letter":"\\u0635","plate_part2":"125","plate_part3":"42","national_code":"0720317444"}	guest	\N	\N	2025-09-08 18:33:57	2025-09-08 18:33:57	req_63E34404074796C1	\N	\N
136	29	\N	{"plate_part1":"72","plate_letter":"\\u0635","plate_part2":"448","plate_part3":"72","national_code":"2190083151"}	guest	\N	\N	2025-09-08 18:34:22	2025-09-08 18:34:22	req_65B03C71602922BE	\N	\N
137	29	\N	{"plate_part1":"94","plate_letter":"\\u0637","plate_part2":"464","plate_part3":"37","national_code":"2921052814"}	guest	\N	\N	2025-09-08 18:34:36	2025-09-08 18:34:36	req_B827101074D8C050	\N	\N
138	29	\N	{"plate_part1":"47","plate_letter":"\\u062c","plate_part2":"293","plate_part3":"57","national_code":"0550187871"}	guest	\N	\N	2025-09-08 18:35:05	2025-09-08 18:35:05	req_F4BF5056335000AB	\N	\N
139	6	\N	{"national_code":"1980342954","mobile":"09050795598"}	guest	\N	\N	2025-09-08 18:35:06	2025-09-08 18:35:06	req_2051FF69F1076E5A	\N	\N
140	29	178	{"plate_part1":"72","plate_letter":"\\u0646","plate_part2":"841","plate_part3":"33","national_code":"0074610201"}	insufficient_balance	\N	\N	2025-09-08 18:36:25	2025-09-08 18:36:25	req_F5E6293DD4D66887	\N	\N
141	29	\N	{"plate_part1":"92","plate_letter":"\\u0635","plate_part2":"569","plate_part3":"84","national_code":"3380205818"}	guest	\N	\N	2025-09-08 18:41:18	2025-09-08 18:41:18	req_D1C386870227C30E	\N	\N
142	29	\N	{"plate_part1":"53","plate_letter":"\\u062c","plate_part2":"838","plate_part3":"69","national_code":"2230295926"}	guest	\N	\N	2025-09-08 18:41:34	2025-09-08 18:41:34	req_A871C18FCDFFEB97	\N	\N
143	29	\N	{"plate_part1":"44","plate_letter":"\\u0648","plate_part2":"647","plate_part3":"72","national_code":"5010913801"}	guest	\N	\N	2025-09-08 18:42:44	2025-09-08 18:42:44	req_4A91646864B790BC	\N	\N
144	29	\N	{"plate_part1":"68","plate_letter":"\\u0646","plate_part2":"853","plate_part3":"14","national_code":"1819920161"}	guest	\N	\N	2025-09-08 18:43:30	2025-09-08 18:43:30	req_767CEB550B9313FB	\N	\N
145	24	\N	{"mobile":"09128050183","national_code":"5660103111"}	guest	\N	\N	2025-09-08 18:43:41	2025-09-08 18:43:41	req_69DFE5EE2507F4D0	\N	\N
146	29	\N	{"plate_part1":"41","plate_letter":"\\u0637","plate_part2":"874","plate_part3":"68","national_code":"0310588936"}	guest	\N	\N	2025-09-08 18:44:39	2025-09-08 18:44:39	req_0F96F1B6ECB799F9	\N	\N
147	29	\N	{"plate_part1":"61","plate_letter":"\\u062c","plate_part2":"676","plate_part3":"29","national_code":"3320186485"}	guest	\N	\N	2025-09-08 18:45:20	2025-09-08 18:45:20	req_FD02FB4707356949	\N	\N
148	6	\N	{"national_code":"0640097243","mobile":"09034905716"}	guest	\N	\N	2025-09-08 18:46:22	2025-09-08 18:46:22	req_8204C85B35260017	\N	\N
149	16	\N	{"national_code":"2001111711","mobile":"09169384996"}	guest	\N	\N	2025-09-08 18:47:29	2025-09-08 18:47:29	req_4554C70394F3633B	\N	\N
150	29	\N	{"plate_part1":"28","plate_letter":"\\u0633","plate_part2":"684","plate_part3":"61","national_code":"3801285197"}	guest	\N	\N	2025-09-08 18:47:32	2025-09-08 18:47:32	req_612BD98E4BDD3D35	\N	\N
151	29	\N	{"plate_part1":"76","plate_letter":"\\u0637","plate_part2":"452","plate_part3":"21","national_code":"4900688606"}	guest	\N	\N	2025-09-08 18:48:11	2025-09-08 18:48:11	req_E3E70527D4F821B5	\N	\N
152	16	\N	{"national_code":"1980357854","mobile":"09999894170"}	guest	\N	\N	2025-09-08 18:48:22	2025-09-08 18:48:22	req_25757F7FAFFC5C1D	\N	\N
153	29	\N	{"plate_part1":"65","plate_letter":"\\u0637","plate_part2":"574","plate_part3":"31","national_code":"4071753080"}	guest	\N	\N	2025-09-08 18:48:57	2025-09-08 18:48:57	req_DC7CC5D8B6910AB5	\N	\N
154	29	\N	{"plate_part1":"62","plate_letter":"\\u0648","plate_part2":"377","plate_part3":"53","national_code":"1080652086"}	guest	\N	\N	2025-09-08 18:49:40	2025-09-08 18:49:40	req_B33B72F261D63C46	\N	\N
155	29	\N	{"plate_part1":"92","plate_letter":"\\u0635","plate_part2":"569","plate_part3":"84","national_code":"3380205818"}	guest	\N	\N	2025-09-08 18:50:02	2025-09-08 18:50:02	req_3CBCB2B559A9FEFA	\N	\N
156	6	\N	{"national_code":"6609561168","mobile":"09907021247"}	guest	\N	\N	2025-09-08 18:50:28	2025-09-08 18:50:28	req_835CBE3E4B1C180B	\N	\N
157	29	180	{"plate_part1":"92","plate_letter":"\\u0635","plate_part2":"569","plate_part3":"84","national_code":"3380205818"}	insufficient_balance	\N	\N	2025-09-08 18:52:48	2025-09-08 18:52:48	req_D70D28823CFD9EAA	\N	\N
158	6	\N	{"national_code":"2593904208","mobile":"09102621121"}	guest	\N	\N	2025-09-08 18:53:09	2025-09-08 18:53:09	req_22E5CF4EEB29C266	\N	\N
159	29	\N	{"plate_part1":"65","plate_letter":"\\u0648","plate_part2":"224","plate_part3":"54","national_code":"4431181393"}	guest	\N	\N	2025-09-08 18:54:48	2025-09-08 18:54:48	req_9CA73FD67609C8AE	\N	\N
160	6	181	[]	insufficient_balance	\N	\N	2025-09-08 18:54:55	2025-09-08 18:54:55	req_446C01AED78F2392	\N	\N
161	6	\N	{"national_code":"0750161469","mobile":"09150851688"}	guest	\N	\N	2025-09-08 18:56:00	2025-09-08 18:56:00	req_3141BFAE6732C789	\N	\N
162	18	\N	{"plate_part1":"66","plate_letter":"\\u06cc","plate_part2":"546","plate_part3":"17"}	guest	\N	\N	2025-09-08 18:56:11	2025-09-08 18:56:11	req_3E520A3EA1C0C965	\N	\N
163	15	\N	{"national_code":"4830005467","mobile":"09112004323"}	guest	\N	\N	2025-09-08 18:56:27	2025-09-08 18:56:27	req_1AA1698FD94E51CE	\N	\N
164	29	\N	{"plate_part1":"37","plate_letter":"\\u0642","plate_part2":"463","plate_part3":"34","national_code":"1810869080"}	guest	\N	\N	2025-09-08 18:56:53	2025-09-08 18:56:53	req_BAD356CB73AA470C	\N	\N
165	29	\N	{"plate_part1":"52","plate_letter":"\\u062f","plate_part2":"543","plate_part3":"85","national_code":"3621432991"}	guest	\N	\N	2025-09-08 18:58:31	2025-09-08 18:58:31	req_7207EC6CA44A9F49	\N	\N
166	6	\N	{"national_code":"5980136703","mobile":"09166364944"}	guest	\N	\N	2025-09-08 18:58:36	2025-09-08 18:58:36	req_0CD5545AB16B350A	\N	\N
167	6	\N	{"national_code":"5980136703","mobile":"09166364944"}	guest	\N	\N	2025-09-08 18:59:20	2025-09-08 18:59:20	req_5572017C8339B1B6	\N	\N
168	29	\N	{"plate_part1":"11","plate_letter":"\\u0645","plate_part2":"239","plate_part3":"38","national_code":"3990132091"}	guest	\N	\N	2025-09-08 19:00:12	2025-09-08 19:00:12	req_56EAEABCB86F8B3C	\N	\N
169	29	\N	{"plate_part1":"31","plate_letter":"\\u0637","plate_part2":"127","plate_part3":"68","national_code":"0312256221"}	guest	\N	\N	2025-09-08 19:01:00	2025-09-08 19:01:00	req_DBDF7451841E9F48	\N	\N
170	29	\N	{"plate_part1":"62","plate_letter":"\\u0645","plate_part2":"279","plate_part3":"78","national_code":"1462181457"}	guest	\N	\N	2025-09-08 19:02:12	2025-09-08 19:02:12	req_8A752D5A6F712057	\N	\N
171	29	\N	{"plate_part1":"34","plate_letter":"\\u0637","plate_part2":"113","plate_part3":"60","national_code":"4898970494"}	guest	\N	\N	2025-09-08 19:03:08	2025-09-08 19:03:08	req_C8B0098687E96B14	\N	\N
172	6	90	[]	insufficient_balance	\N	\N	2025-09-08 19:05:49	2025-09-08 19:05:49	req_6C99FD4BD35F0D3D	\N	\N
173	29	\N	{"plate_part1":"24","plate_letter":"\\u0646","plate_part2":"885","plate_part3":"84","national_code":"3382640971"}	guest	\N	\N	2025-09-08 19:06:39	2025-09-08 19:06:39	req_325CB8E6E2AFF2A4	\N	\N
174	6	\N	{"national_code":"5749967638","mobile":"09024091910"}	guest	\N	\N	2025-09-08 19:06:53	2025-09-08 19:06:53	req_4BEA97DB6145895C	\N	\N
175	29	\N	{"plate_part1":"72","plate_letter":"\\u0635","plate_part2":"448","plate_part3":"72","national_code":"2190083151"}	guest	\N	\N	2025-09-08 19:07:22	2025-09-08 19:07:22	req_D8B166C096ED9BD9	\N	\N
176	29	\N	{"plate_part1":"31","plate_letter":"\\u0644","plate_part2":"381","plate_part3":"99","national_code":"0534966489"}	guest	\N	\N	2025-09-08 19:08:07	2025-09-08 19:08:07	req_5D07B8758E8F7C7D	\N	\N
177	29	\N	{"plate_part1":"67","plate_letter":"\\u0635","plate_part2":"459","plate_part3":"99","national_code":"0078771730"}	guest	\N	\N	2025-09-08 19:08:26	2025-09-08 19:08:26	req_D1A1D1CDF6F8A097	\N	\N
178	29	\N	{"plate_part1":"11","plate_letter":"\\u0637","plate_part2":"984","plate_part3":"61","national_code":"0064483797"}	guest	\N	\N	2025-09-08 19:08:27	2025-09-08 19:08:27	req_CB315414822BA08E	\N	\N
179	6	\N	{"national_code":"5749967638","mobile":"09024091910"}	guest	\N	\N	2025-09-08 19:09:28	2025-09-08 19:09:28	req_8DF0B4CBF8433C94	\N	\N
180	29	\N	{"plate_part1":"11","plate_letter":"\\u0637","plate_part2":"984","plate_part3":"61","national_code":"0064483797"}	guest	\N	\N	2025-09-08 19:09:47	2025-09-08 19:09:47	req_B3E02C8398CA5282	\N	\N
181	6	\N	{"national_code":"0922677638","mobile":"09360497986"}	guest	\N	\N	2025-09-08 19:09:54	2025-09-08 19:09:54	req_2ABC09345B093A1A	\N	\N
182	29	\N	{"plate_part1":"53","plate_letter":"\\u0628","plate_part2":"169","plate_part3":"24","national_code":"1819554783"}	guest	\N	\N	2025-09-08 19:10:28	2025-09-08 19:10:28	req_43CB020B682EFFDE	\N	\N
183	6	\N	{"national_code":"2150562995","mobile":"09118640156"}	guest	\N	\N	2025-09-08 19:11:25	2025-09-08 19:11:25	req_A9469E2A7B17AF9D	\N	\N
184	29	\N	{"plate_part1":"28","plate_letter":"\\u0647","plate_part2":"774","plate_part3":"33","national_code":"0054326109"}	guest	\N	\N	2025-09-08 19:11:30	2025-09-08 19:11:30	req_C4E05C12208C75E6	\N	\N
185	29	\N	{"plate_part1":"89","plate_letter":"\\u062c","plate_part2":"794","plate_part3":"27","national_code":"2871958157"}	guest	\N	\N	2025-09-08 19:12:19	2025-09-08 19:12:19	req_77C671E7A1643139	\N	\N
186	29	\N	{"plate_part1":"85","plate_letter":"\\u0642","plate_part2":"334","plate_part3":"15","national_code":"1372770194"}	guest	\N	\N	2025-09-08 19:12:45	2025-09-08 19:12:45	req_385C5DA74B191B0C	\N	\N
187	6	\N	{"national_code":"3241975541","mobile":"09397671207"}	guest	\N	\N	2025-09-08 19:13:00	2025-09-08 19:13:00	req_46429EC15C1DF4D3	\N	\N
188	29	\N	{"plate_part1":"13","plate_letter":"\\u0637","plate_part2":"254","plate_part3":"13","national_code":"1287264530"}	guest	\N	\N	2025-09-08 19:13:12	2025-09-08 19:13:12	req_250C4C21A81FC149	\N	\N
189	6	90	[]	insufficient_balance	\N	\N	2025-09-08 19:13:40	2025-09-08 19:13:40	req_867F1F0C3C93EFA7	\N	\N
190	29	\N	{"plate_part1":"37","plate_letter":"\\u0646","plate_part2":"321","plate_part3":"50","national_code":"4251311345"}	guest	\N	\N	2025-09-08 19:14:38	2025-09-08 19:14:38	req_40706AB972DE1891	\N	\N
191	29	\N	{"plate_part1":"39","plate_letter":"\\u062f","plate_part2":"813","plate_part3":"13","national_code":"1270164279"}	guest	\N	\N	2025-09-08 19:14:49	2025-09-08 19:14:49	req_38637AE1C2CCBF17	\N	\N
192	6	\N	{"national_code":"1741476623","mobile":"09106486276"}	guest	\N	\N	2025-09-08 19:16:41	2025-09-08 19:16:41	req_0E2D2612886A1188	\N	\N
193	29	\N	{"plate_part1":"72","plate_letter":"\\u06cc","plate_part2":"146","plate_part3":"68","national_code":"0061379271"}	guest	\N	\N	2025-09-08 19:16:53	2025-09-08 19:16:53	req_89DA47BA6A7F11F6	\N	\N
194	29	\N	{"plate_part1":"64","plate_letter":"\\u0642","plate_part2":"421","plate_part3":"84","national_code":"3380985414"}	guest	\N	\N	2025-09-08 19:17:24	2025-09-08 19:17:24	req_C9DD6442F7B39B31	\N	\N
195	16	\N	{"national_code":"1130369404","mobile":"09140079409"}	guest	\N	\N	2025-09-08 19:17:45	2025-09-08 19:17:45	req_19A96A131BC4A620	\N	\N
196	29	183	{"plate_part1":"85","plate_letter":"\\u0642","plate_part2":"334","plate_part3":"15","national_code":"1372770194"}	insufficient_balance	\N	\N	2025-09-08 19:18:23	2025-09-08 19:18:23	req_548E957CC467EB12	\N	\N
197	16	\N	{"national_code":"2971839461","mobile":"09143017049"}	guest	\N	\N	2025-09-08 19:19:57	2025-09-08 19:19:57	req_9E32E812FD9D1BEB	\N	\N
198	29	\N	{"plate_part1":"21","plate_letter":"\\u062a","plate_part2":"179","plate_part3":"39","national_code":"4132721353"}	guest	\N	\N	2025-09-08 19:20:31	2025-09-08 19:20:31	req_EF8B96D0002E2C34	\N	\N
199	29	\N	{"plate_part1":"12","plate_letter":"\\u06cc","plate_part2":"549","plate_part3":"32","national_code":"1051097691"}	guest	\N	\N	2025-09-08 19:21:10	2025-09-08 19:21:10	req_485C0E1ED954C4B6	\N	\N
200	29	\N	{"plate_part1":"34","plate_letter":"\\u0642","plate_part2":"597","plate_part3":"68","national_code":"4899880375"}	guest	\N	\N	2025-09-08 19:21:44	2025-09-08 19:21:44	req_8A54E89B07F97EEA	\N	\N
201	29	\N	{"plate_part1":"37","plate_letter":"\\u062f","plate_part2":"553","plate_part3":"88","national_code":"2679457617"}	guest	\N	\N	2025-09-08 19:22:19	2025-09-08 19:22:19	req_3A19D9E6AD65005F	\N	\N
202	29	\N	{"plate_part1":"26","plate_letter":"\\u0644","plate_part2":"716","plate_part3":"50","national_code":"0451735552"}	guest	\N	\N	2025-09-08 19:22:28	2025-09-08 19:22:28	req_02C1589175B0238B	\N	\N
203	29	\N	{"plate_part1":"26","plate_letter":"\\u0633","plate_part2":"558","plate_part3":"54","national_code":"4432709316"}	guest	\N	\N	2025-09-08 19:22:38	2025-09-08 19:22:38	req_AA5AFF7A27431B79	\N	\N
204	29	\N	{"plate_part1":"32","plate_letter":"\\u0645","plate_part2":"163","plate_part3":"15","national_code":"1381828140"}	guest	\N	\N	2025-09-08 19:23:30	2025-09-08 19:23:30	req_429759B14E4A0E2F	\N	\N
205	29	184	{"plate_part1":"26","plate_letter":"\\u0633","plate_part2":"558","plate_part3":"54","national_code":"4432709316"}	insufficient_balance	\N	\N	2025-09-08 19:23:32	2025-09-08 19:23:32	req_6B33B9BB4C62A3D1	\N	\N
206	29	\N	{"plate_part1":"84","plate_letter":"\\u0642","plate_part2":"899","plate_part3":"85","national_code":"3611925780"}	guest	\N	\N	2025-09-08 19:26:40	2025-09-08 19:26:40	req_DD74284A347CE4D4	\N	\N
207	29	\N	{"plate_part1":"93","plate_letter":"\\u0645","plate_part2":"785","plate_part3":"10","national_code":"0311044026"}	guest	\N	\N	2025-09-08 19:26:44	2025-09-08 19:26:44	req_47B5D0C412EDE508	\N	\N
208	29	\N	{"plate_part1":"97","plate_letter":"\\u0637","plate_part2":"696","plate_part3":"40","national_code":"0010746064"}	guest	\N	\N	2025-09-08 19:27:38	2025-09-08 19:27:38	req_6F20781CE2BC8ECD	\N	\N
209	17	\N	{"national_code":"1621253538","mobile":"09145380759"}	guest	\N	\N	2025-09-08 19:28:58	2025-09-08 19:28:58	req_5E1C4135F103C55F	\N	\N
210	29	\N	{"plate_part1":"91","plate_letter":"\\u062f","plate_part2":"817","plate_part3":"91","national_code":"1466184442"}	guest	\N	\N	2025-09-08 19:29:07	2025-09-08 19:29:07	req_6FF9A8CC1923C007	\N	\N
211	29	\N	{"plate_part1":"21","plate_letter":"\\u0648","plate_part2":"411","plate_part3":"16","national_code":"0372633560"}	guest	\N	\N	2025-09-08 19:29:23	2025-09-08 19:29:23	req_E58471305F6F5E35	\N	\N
212	29	\N	{"plate_part1":"32","plate_letter":"\\u0645","plate_part2":"442","plate_part3":"47","national_code":"0534557430"}	guest	\N	\N	2025-09-08 19:31:21	2025-09-08 19:31:21	req_C31AF0F6CC05A65B	\N	\N
213	29	\N	{"plate_part1":"85","plate_letter":"\\u0642","plate_part2":"778","plate_part3":"66","national_code":"0323590012"}	guest	\N	\N	2025-09-08 19:31:42	2025-09-08 19:31:42	req_802C4303AD84A3C1	\N	\N
214	29	\N	{"plate_part1":"53","plate_letter":"\\u0637","plate_part2":"757","plate_part3":"47","national_code":"4210065382"}	guest	\N	\N	2025-09-08 19:33:49	2025-09-08 19:33:49	req_4E9FC6B31030EB99	\N	\N
215	29	\N	{"plate_part1":"31","plate_letter":"\\u0633","plate_part2":"811","plate_part3":"76","national_code":"2640270702"}	guest	\N	\N	2025-09-08 19:34:30	2025-09-08 19:34:30	req_689CFBCBA500D3A7	\N	\N
216	29	\N	{"plate_part1":"86","plate_letter":"\\u0642","plate_part2":"359","plate_part3":"87","national_code":"4271525766"}	guest	\N	\N	2025-09-08 19:34:53	2025-09-08 19:34:53	req_2A6895F1F387AB3C	\N	\N
217	29	\N	{"plate_part1":"56","plate_letter":"\\u062c","plate_part2":"842","plate_part3":"97","national_code":"4360570295"}	guest	\N	\N	2025-09-08 19:36:20	2025-09-08 19:36:20	req_C438084BA128DAEA	\N	\N
218	29	\N	{"plate_part1":"71","plate_letter":"\\u0633","plate_part2":"879","plate_part3":"15","national_code":"1717310524"}	guest	\N	\N	2025-09-08 19:37:21	2025-09-08 19:37:21	req_DA49A42826D7789A	\N	\N
219	29	\N	{"plate_part1":"77","plate_letter":"\\u0642","plate_part2":"447","plate_part3":"77","national_code":"0075649160"}	guest	\N	\N	2025-09-08 19:38:25	2025-09-08 19:38:25	req_1E3F9A68B660796A	\N	\N
220	6	\N	{"national_code":"0078923050","mobile":"09198184270"}	guest	\N	\N	2025-09-08 19:38:32	2025-09-08 19:38:32	req_644CC91BDDD4B057	\N	\N
221	29	\N	{"plate_part1":"62","plate_letter":"\\u0642","plate_part2":"447","plate_part3":"77","national_code":"0075649160"}	guest	\N	\N	2025-09-08 19:39:38	2025-09-08 19:39:38	req_189C9E9DEBBC4024	\N	\N
222	29	185	{"plate_part1":"71","plate_letter":"\\u0633","plate_part2":"879","plate_part3":"15","national_code":"1717310524"}	insufficient_balance	\N	\N	2025-09-08 19:39:41	2025-09-08 19:39:41	req_CF4485BCEBF887E5	\N	\N
223	29	\N	{"plate_part1":"77","plate_letter":"\\u0642","plate_part2":"447","plate_part3":"62","national_code":"0075649160"}	guest	\N	\N	2025-09-08 19:40:27	2025-09-08 19:40:27	req_6D4A28A082057E51	\N	\N
224	29	\N	{"plate_part1":"62","plate_letter":"\\u0642","plate_part2":"447","plate_part3":"77","national_code":"0075649160"}	guest	\N	\N	2025-09-08 19:41:25	2025-09-08 19:41:25	req_5153EBC83AAA7B49	\N	\N
225	29	\N	{"plate_part1":"44","plate_letter":"\\u0645","plate_part2":"682","plate_part3":"38","national_code":"4060404901"}	guest	\N	\N	2025-09-08 19:41:31	2025-09-08 19:41:31	req_91853E85ECA2E8BA	\N	\N
226	29	\N	{"plate_part1":"14","plate_letter":"\\u0633","plate_part2":"882","plate_part3":"68","national_code":"1810008255"}	guest	\N	\N	2025-09-08 19:41:33	2025-09-08 19:41:33	req_14584BBC476340F5	\N	\N
227	6	\N	{"national_code":"2281246991","mobile":"09179257647"}	guest	\N	\N	2025-09-08 19:42:14	2025-09-08 19:42:14	req_73BF1E045271F970	\N	\N
228	29	\N	{"plate_part1":"92","plate_letter":"\\u0633","plate_part2":"252","plate_part3":"33","national_code":"1120065097"}	guest	\N	\N	2025-09-08 19:43:13	2025-09-08 19:43:13	req_04C570069E736DE8	\N	\N
229	6	\N	{"national_code":"1757567119","mobile":"09386746466"}	guest	\N	\N	2025-09-08 19:44:09	2025-09-08 19:44:09	req_41653154694EDA5B	\N	\N
230	29	\N	{"plate_part1":"13","plate_letter":"\\u06cc","plate_part2":"759","plate_part3":"75","national_code":"1272615741"}	guest	\N	\N	2025-09-08 19:44:36	2025-09-08 19:44:36	req_94E3072A8019771D	\N	\N
231	16	\N	{"national_code":"1810356581","mobile":"09168741748"}	guest	\N	\N	2025-09-08 19:44:58	2025-09-08 19:44:58	req_50D7FD2E70761A61	\N	\N
232	29	\N	{"plate_part1":"13","plate_letter":"\\u0647","plate_part2":"476","plate_part3":"17","national_code":"2752542364"}	guest	\N	\N	2025-09-08 19:46:05	2025-09-08 19:46:05	req_5861CC60F4469E8A	\N	\N
233	6	\N	{"national_code":"2559199149","mobile":"09170614139"}	guest	\N	\N	2025-09-08 19:46:14	2025-09-08 19:46:14	req_32C6973FD1C2B9FA	\N	\N
234	6	\N	{"national_code":"3309813215","mobile":"09183565647"}	guest	\N	\N	2025-09-08 19:47:25	2025-09-08 19:47:25	req_1758A283CD4F1E20	\N	\N
235	29	\N	{"plate_part1":"92","plate_letter":"\\u0645","plate_part2":"293","plate_part3":"24","national_code":"1930795696"}	guest	\N	\N	2025-09-08 19:47:58	2025-09-08 19:47:58	req_040F19E751578A0A	\N	\N
236	29	\N	{"plate_part1":"62","plate_letter":"\\u062f","plate_part2":"139","plate_part3":"73","national_code":"2480674126"}	guest	\N	\N	2025-09-08 19:48:36	2025-09-08 19:48:36	req_6C0CEFED580DE429	\N	\N
237	16	\N	{"national_code":"2559199149","mobile":"09170614139"}	guest	\N	\N	2025-09-08 19:48:46	2025-09-08 19:48:46	req_98E4D894BDF25512	\N	\N
238	29	\N	{"plate_part1":"34","plate_letter":"\\u0635","plate_part2":"922","plate_part3":"60","national_code":"0046014098"}	guest	\N	\N	2025-09-08 19:49:51	2025-09-08 19:49:51	req_141AC6589A61323E	\N	\N
239	6	\N	{"national_code":"3839042518","mobile":"09907412026"}	guest	\N	\N	2025-09-08 19:50:49	2025-09-08 19:50:49	req_2136482860F13114	\N	\N
240	29	\N	{"plate_part1":"73","plate_letter":"\\u0646","plate_part2":"987","plate_part3":"55","national_code":"0074219091"}	guest	\N	\N	2025-09-08 19:52:31	2025-09-08 19:52:31	req_002F26687ECBCF07	\N	\N
241	384	\N	{"national_code":"3839042518","mobile":"09907412026"}	guest	\N	\N	2025-09-08 19:52:46	2025-09-08 19:52:46	req_768CF3C40D5F79D5	\N	\N
242	29	\N	{"plate_part1":"64","plate_letter":"\\u0642","plate_part2":"421","plate_part3":"84","national_code":"3380985414"}	guest	\N	\N	2025-09-08 19:52:58	2025-09-08 19:52:58	req_010C4E07CF65A02E	\N	\N
243	29	\N	{"plate_part1":"57","plate_letter":"\\u0647","plate_part2":"863","plate_part3":"13","national_code":"1283047225"}	guest	\N	\N	2025-09-08 19:55:36	2025-09-08 19:55:36	req_984265BA05C32704	\N	\N
244	29	\N	{"plate_part1":"49","plate_letter":"\\u0645","plate_part2":"475","plate_part3":"30","national_code":"0779971469"}	guest	\N	\N	2025-09-08 19:55:45	2025-09-08 19:55:45	req_0E9DA2FE388DEC39	\N	\N
245	6	\N	{"national_code":"3100429508","mobile":"09307877480"}	guest	\N	\N	2025-09-08 19:56:09	2025-09-08 19:56:09	req_292791F8A743C100	\N	\N
246	6	\N	{"national_code":"2691134301","mobile":"09350799357"}	guest	\N	\N	2025-09-08 19:56:38	2025-09-08 19:56:38	req_70FEBFC338BB9955	\N	\N
247	6	\N	{"national_code":"4132200140","mobile":"09026603820"}	guest	\N	\N	2025-09-08 19:56:58	2025-09-08 19:56:58	req_F9613D5A53717625	\N	\N
248	29	\N	{"plate_part1":"57","plate_letter":"\\u0647","plate_part2":"863","plate_part3":"13","national_code":"1283047225"}	guest	\N	\N	2025-09-08 19:57:49	2025-09-08 19:57:49	req_921939176997FCB9	\N	\N
249	6	\N	{"national_code":"4132200140","mobile":"09026603820"}	guest	\N	\N	2025-09-08 19:58:06	2025-09-08 19:58:06	req_FEA030BA2B0ECC1A	\N	\N
250	6	\N	{"national_code":"0073956430","mobile":"09125906319"}	guest	\N	\N	2025-09-08 20:00:39	2025-09-08 20:00:39	req_6513BFA832255B77	\N	\N
251	29	\N	{"plate_part1":"49","plate_letter":"\\u0645","plate_part2":"475","plate_part3":"30","national_code":"0779971469"}	guest	\N	\N	2025-09-08 20:01:24	2025-09-08 20:01:24	req_7F65823409A879EA	\N	\N
252	29	\N	{"plate_part1":"55","plate_letter":"\\u0642","plate_part2":"795","plate_part3":"23","national_code":"1180123123"}	guest	\N	\N	2025-09-08 20:03:14	2025-09-08 20:03:14	req_8BF9F7EAF3B8097C	\N	\N
253	29	\N	{"plate_part1":"13","plate_letter":"\\u0647","plate_part2":"476","plate_part3":"17","national_code":"2752542364"}	guest	\N	\N	2025-09-08 20:04:16	2025-09-08 20:04:16	req_3D6F1077D9D1E63A	\N	\N
254	6	\N	{"national_code":"5169411251","mobile":"09111823030"}	guest	\N	\N	2025-09-08 20:04:31	2025-09-08 20:04:31	req_A98236D7B4928894	\N	\N
255	29	\N	{"plate_part1":"49","plate_letter":"\\u0645","plate_part2":"475","plate_part3":"30","national_code":"0779971469"}	guest	\N	\N	2025-09-08 20:04:47	2025-09-08 20:04:47	req_9D4E692B6C9A746C	\N	\N
256	6	\N	{"national_code":"3420449895","mobile":"09138374493"}	guest	\N	\N	2025-09-08 20:05:44	2025-09-08 20:05:44	req_5C689E2F7CAE6EB8	\N	\N
257	29	\N	{"plate_part1":"12","plate_letter":"\\u06cc","plate_part2":"195","plate_part3":"20","national_code":"0059293047"}	guest	\N	\N	2025-09-08 20:05:46	2025-09-08 20:05:46	req_E7D32A8551D5EA52	\N	\N
258	29	\N	{"plate_part1":"12","plate_letter":"\\u0647","plate_part2":"414","plate_part3":"91","national_code":"0946616787"}	guest	\N	\N	2025-09-08 20:08:09	2025-09-08 20:08:09	req_6075AA93ED6F4FED	\N	\N
259	29	\N	{"plate_part1":"24","plate_letter":"\\u0642","plate_part2":"931","plate_part3":"97","national_code":"4411005689"}	guest	\N	\N	2025-09-08 20:08:14	2025-09-08 20:08:14	req_687A14EF1EAB6B05	\N	\N
260	29	187	{"plate_part1":"93","plate_letter":"\\u0642","plate_part2":"182","plate_part3":"55","national_code":"0150961332"}	insufficient_balance	\N	\N	2025-09-08 20:08:29	2025-09-08 20:08:29	req_998FE92AA45D7B43	\N	\N
261	29	\N	{"plate_part1":"37","plate_letter":"\\u0647","plate_part2":"843","plate_part3":"13","national_code":"1285888251"}	guest	\N	\N	2025-09-08 20:09:29	2025-09-08 20:09:29	req_EF939E6661507E05	\N	\N
262	29	\N	{"plate_part1":"38","plate_letter":"\\u062f","plate_part2":"326","plate_part3":"36","national_code":"5749055930"}	guest	\N	\N	2025-09-08 20:10:18	2025-09-08 20:10:18	req_80D5476D6E72B43C	\N	\N
263	29	\N	{"plate_part1":"44","plate_letter":"\\u062c","plate_part2":"966","plate_part3":"72","national_code":"2051244170"}	guest	\N	\N	2025-09-08 20:11:05	2025-09-08 20:11:05	req_133B118E0FD49FC7	\N	\N
264	29	\N	{"plate_part1":"91","plate_letter":"\\u0647","plate_part2":"41","plate_part3":"12","national_code":"0946616787"}	guest	\N	\N	2025-09-08 20:11:08	2025-09-08 20:11:08	req_CA691DBBB6E13399	\N	\N
265	6	\N	{"national_code":"2580134468","mobile":"09112483254"}	guest	\N	\N	2025-09-08 20:11:33	2025-09-08 20:11:33	req_46D84CC49A98E7FF	\N	\N
266	6	\N	{"national_code":"0480676089","mobile":"09195664360"}	guest	\N	\N	2025-09-08 20:11:48	2025-09-08 20:11:48	req_1494A9F6FD626AD4	\N	\N
267	29	\N	{"plate_part1":"73","plate_letter":"\\u0646","plate_part2":"987","plate_part3":"55","national_code":"0074219091"}	guest	\N	\N	2025-09-08 20:13:00	2025-09-08 20:13:00	req_DF78000E9A10C8D0	\N	\N
268	29	\N	{"plate_part1":"12","plate_letter":"\\u0647","plate_part2":"726","plate_part3":"33","national_code":"1719613540"}	guest	\N	\N	2025-09-08 20:13:17	2025-09-08 20:13:17	req_025D7E0E6226AB77	\N	\N
269	6	\N	{"national_code":"0480676089","mobile":"09195664360"}	guest	\N	\N	2025-09-08 20:13:47	2025-09-08 20:13:47	req_FC3B5157311070DE	\N	\N
270	29	\N	{"plate_part1":"96","plate_letter":"\\u062a","plate_part2":"296","plate_part3":"22","national_code":"0013703730"}	guest	\N	\N	2025-09-08 20:14:08	2025-09-08 20:14:08	req_7D15388C08B5118E	\N	\N
271	29	\N	{"plate_part1":"84","plate_letter":"\\u062f","plate_part2":"313","plate_part3":"67","national_code":"1280333774"}	guest	\N	\N	2025-09-08 20:14:51	2025-09-08 20:14:51	req_8F748E83863DF364	\N	\N
272	29	\N	{"plate_part1":"46","plate_letter":"\\u0646","plate_part2":"922","plate_part3":"20","national_code":"1450779204"}	guest	\N	\N	2025-09-08 20:15:04	2025-09-08 20:15:04	req_1F0E95C41611680C	\N	\N
273	6	188	[]	insufficient_balance	\N	\N	2025-09-08 20:15:43	2025-09-08 20:15:43	req_049BDFC43C43BC4D	\N	\N
274	6	109	[]	insufficient_balance	\N	\N	2025-09-08 20:16:28	2025-09-08 20:16:28	req_DDA396568536B1F2	\N	\N
275	18	\N	{"plate_part1":"41","plate_letter":"\\u0645","plate_part2":"721","plate_part3":"47"}	guest	\N	\N	2025-09-08 20:18:09	2025-09-08 20:18:09	req_B7F7C939794D7388	\N	\N
276	29	\N	{"plate_part1":"74","plate_letter":"\\u06cc","plate_part2":"971","plate_part3":"78","national_code":"0071178104"}	guest	\N	\N	2025-09-08 20:20:19	2025-09-08 20:20:19	req_8CA8EC807770852E	\N	\N
277	31	\N	{"national_code":"0068356277","mobile":"09121126186"}	guest	\N	\N	2025-09-08 20:20:27	2025-09-08 20:20:27	req_0988C110C779A45B	\N	\N
278	3	\N	{"account_number":"0117371689","bank_id":"18"}	guest	\N	\N	2025-09-08 20:20:58	2025-09-08 20:20:58	req_0D9AACCDAF926B60	\N	\N
279	29	\N	{"plate_part1":"86","plate_letter":"\\u0633","plate_part2":"218","plate_part3":"38","national_code":"4592146468"}	guest	\N	\N	2025-09-08 20:21:32	2025-09-08 20:21:32	req_F3472DABA6880B0E	\N	\N
280	6	188	[]	insufficient_balance	\N	\N	2025-09-08 20:21:33	2025-09-08 20:21:33	req_EF0765457BCE5986	\N	\N
281	84	188	[]	insufficient_balance	\N	\N	2025-09-08 20:21:49	2025-09-08 20:21:49	req_F2F4706175AD80CC	\N	\N
282	29	\N	{"plate_part1":"21","plate_letter":"\\u0647","plate_part2":"547","plate_part3":"45","national_code":"2993850059"}	guest	\N	\N	2025-09-08 20:22:17	2025-09-08 20:22:17	req_6816407E74209F5E	\N	\N
283	29	\N	{"plate_part1":"72","plate_letter":"\\u0633","plate_part2":"959","plate_part3":"89","national_code":"4380330729"}	guest	\N	\N	2025-09-08 20:23:05	2025-09-08 20:23:05	req_13534793A18957D9	\N	\N
284	6	\N	{"national_code":"0020246218","mobile":"09125854986"}	guest	\N	\N	2025-09-08 20:26:38	2025-09-08 20:26:38	req_20352E6566235D7C	\N	\N
285	6	\N	{"national_code":"0080114881","mobile":"09038468981"}	guest	\N	\N	2025-09-08 20:27:03	2025-09-08 20:27:03	req_6718AB8846516E46	\N	\N
286	29	\N	{"plate_part1":"45","plate_letter":"\\u0647","plate_part2":"985","plate_part3":"88","national_code":"0012504696"}	guest	\N	\N	2025-09-08 20:27:58	2025-09-08 20:27:58	req_51409AADAC1D89B3	\N	\N
287	29	\N	{"plate_part1":"54","plate_letter":"\\u0635","plate_part2":"874","plate_part3":"57","national_code":"0579961222"}	guest	\N	\N	2025-09-08 20:29:12	2025-09-08 20:29:12	req_E05BF777E3C89E2C	\N	\N
288	29	\N	{"plate_part1":"47","plate_letter":"\\u0635","plate_part2":"424","plate_part3":"60","national_code":"0024003311"}	guest	\N	\N	2025-09-08 20:32:14	2025-09-08 20:32:14	req_5576B9A9A629D71C	\N	\N
289	29	\N	{"plate_part1":"65","plate_letter":"\\u06cc","plate_part2":"996","plate_part3":"12","national_code":"0923551247"}	guest	\N	\N	2025-09-08 20:33:50	2025-09-08 20:33:50	req_CCFDDA3AED40BD19	\N	\N
290	29	\N	{"plate_part1":"55","plate_letter":"\\u0646","plate_part2":"623","plate_part3":"27","national_code":"2929506954"}	guest	\N	\N	2025-09-08 20:34:52	2025-09-08 20:34:52	req_3B8EF0F76E053C22	\N	\N
291	29	\N	{"plate_part1":"55","plate_letter":"\\u0646","plate_part2":"623","plate_part3":"27","national_code":"2929506954"}	guest	\N	\N	2025-09-08 20:36:03	2025-09-08 20:36:03	req_43BD5F75146CFE2E	\N	\N
292	29	\N	{"plate_part1":"66","plate_letter":"\\u0646","plate_part2":"694","plate_part3":"82","national_code":"5820123263"}	guest	\N	\N	2025-09-08 20:36:45	2025-09-08 20:36:45	req_95319FCE8B97D003	\N	\N
293	6	\N	{"national_code":"6229903326","mobile":"09036821462"}	guest	\N	\N	2025-09-08 20:37:40	2025-09-08 20:37:40	req_2045707847778BD2	\N	\N
294	29	\N	{"plate_part1":"73","plate_letter":"\\u0645","plate_part2":"218","plate_part3":"84","national_code":"3020380510"}	guest	\N	\N	2025-09-08 20:37:49	2025-09-08 20:37:49	req_588DEC2FA2E30AB1	\N	\N
295	29	\N	{"plate_part1":"94","plate_letter":"\\u062f","plate_part2":"289","plate_part3":"64","national_code":"4489704909"}	guest	\N	\N	2025-09-08 20:39:08	2025-09-08 20:39:08	req_2540099B436477B3	\N	\N
296	6	39	[]	insufficient_balance	\N	\N	2025-09-08 20:39:36	2025-09-08 20:39:36	req_C326099AED2C7794	\N	\N
297	29	\N	{"plate_part1":"57","plate_letter":"\\u0633","plate_part2":"857","plate_part3":"72","national_code":"2270065751"}	guest	\N	\N	2025-09-08 20:39:49	2025-09-08 20:39:49	req_DC83A2E27957A48E	\N	\N
298	29	\N	{"plate_part1":"94","plate_letter":"\\u0645","plate_part2":"997","plate_part3":"55","national_code":"2739933861"}	guest	\N	\N	2025-09-08 20:39:50	2025-09-08 20:39:50	req_D1DF1AAFB4F426B5	\N	\N
299	29	\N	{"plate_part1":"17","plate_letter":"\\u0637","plate_part2":"942","plate_part3":"38","national_code":"3781390403"}	guest	\N	\N	2025-09-08 20:40:33	2025-09-08 20:40:33	req_1D63B7C5A134B831	\N	\N
300	29	\N	{"plate_part1":"43","plate_letter":"\\u0642","plate_part2":"577","plate_part3":"32","national_code":"0919481450"}	guest	\N	\N	2025-09-08 20:41:06	2025-09-08 20:41:06	req_C4AC1E21206598D3	\N	\N
301	6	\N	{"national_code":"2391998929","mobile":"09179175244"}	guest	\N	\N	2025-09-08 20:41:34	2025-09-08 20:41:34	req_07BFA4A1BAE975AA	\N	\N
302	29	\N	{"plate_part1":"57","plate_letter":"\\u0646","plate_part2":"486","plate_part3":"63","national_code":"2283556899"}	guest	\N	\N	2025-09-08 20:42:03	2025-09-08 20:42:03	req_D0E0A1325C8378E8	\N	\N
303	29	\N	{"plate_part1":"51","plate_letter":"\\u0644","plate_part2":"379","plate_part3":"24","national_code":"1911799932"}	guest	\N	\N	2025-09-08 20:42:07	2025-09-08 20:42:07	req_284D87CD81898DE6	\N	\N
304	29	\N	{"plate_part1":"22","plate_letter":"\\u0639","plate_part2":"263","plate_part3":"93","national_code":"2572135892"}	guest	\N	\N	2025-09-08 20:42:12	2025-09-08 20:42:12	req_8BE9EC1CAC4912BB	\N	\N
305	29	\N	{"plate_part1":"77","plate_letter":"\\u0635","plate_part2":"617","plate_part3":"38","national_code":"2269482417"}	guest	\N	\N	2025-09-08 20:42:24	2025-09-08 20:42:24	req_B8E8496C1C61E6E0	\N	\N
306	29	\N	{"plate_part1":"93","plate_letter":"\\u0642","plate_part2":"446","plate_part3":"23","national_code":"1159543747"}	guest	\N	\N	2025-09-08 20:42:56	2025-09-08 20:42:56	req_D91B843FB7FE42E4	\N	\N
307	29	\N	{"plate_part1":"93","plate_letter":"\\u062f","plate_part2":"975","plate_part3":"40","national_code":"4131971453"}	guest	\N	\N	2025-09-08 20:46:09	2025-09-08 20:46:09	req_C66C70A681F8C616	\N	\N
308	29	\N	{"plate_part1":"92","plate_letter":"\\u0645","plate_part2":"955","plate_part3":"46","national_code":"2590431988"}	guest	\N	\N	2025-09-08 20:46:27	2025-09-08 20:46:27	req_9D1877C7490E5E5F	\N	\N
309	29	\N	{"plate_part1":"36","plate_letter":"\\u0646","plate_part2":"582","plate_part3":"93","national_code":"6489829231"}	guest	\N	\N	2025-09-08 20:46:44	2025-09-08 20:46:44	req_F20DC76996B53131	\N	\N
310	29	\N	{"plate_part1":"93","plate_letter":"\\u062f","plate_part2":"974","plate_part3":"40","national_code":"4131971453"}	guest	\N	\N	2025-09-08 20:47:03	2025-09-08 20:47:03	req_D61D5EB918D912FD	\N	\N
311	29	\N	{"plate_part1":"66","plate_letter":"\\u0645","plate_part2":"747","plate_part3":"24","national_code":"4061207032"}	guest	\N	\N	2025-09-08 20:47:18	2025-09-08 20:47:18	req_1C34C21456694C31	\N	\N
312	29	\N	{"plate_part1":"38","plate_letter":"\\u062c","plate_part2":"872","plate_part3":"86","national_code":"5560145051"}	guest	\N	\N	2025-09-08 20:47:27	2025-09-08 20:47:27	req_279EB2BAD6C665F5	\N	\N
313	29	\N	{"plate_part1":"91","plate_letter":"\\u0646","plate_part2":"385","plate_part3":"74","national_code":"0779948981"}	guest	\N	\N	2025-09-08 20:47:47	2025-09-08 20:47:47	req_6E85BEB2EE7440F2	\N	\N
314	29	\N	{"plate_part1":"24","plate_letter":"\\u0645","plate_part2":"747","plate_part3":"66","national_code":"4061207032"}	guest	\N	\N	2025-09-08 20:48:17	2025-09-08 20:48:17	req_694EE47A7CDBBE9B	\N	\N
315	29	\N	{"plate_part1":"93","plate_letter":"\\u062f","plate_part2":"974","plate_part3":"40","national_code":"4131971453"}	guest	\N	\N	2025-09-08 20:48:35	2025-09-08 20:48:35	req_C73338F852E61D3E	\N	\N
316	29	\N	{"plate_part1":"60","plate_letter":"\\u0644","plate_part2":"796","plate_part3":"37","national_code":"0370455339"}	guest	\N	\N	2025-09-08 20:51:22	2025-09-08 20:51:22	req_320428CB1D2849C4	\N	\N
317	10	5	{"national_code":"0924254742","mobile":"09153887809"}	insufficient_balance	\N	\N	2025-09-08 20:52:40	2025-09-08 20:52:40	req_D6F2391A5EB3F945	\N	\N
318	29	\N	{"plate_part1":"15","plate_letter":"\\u0648","plate_part2":"758","plate_part3":"60","national_code":"2721454358"}	guest	\N	\N	2025-09-08 20:53:08	2025-09-08 20:53:08	req_70C4B897E85DF321	\N	\N
319	6	\N	{"national_code":"3120352926","mobile":"09902008454"}	guest	\N	\N	2025-09-08 20:53:20	2025-09-08 20:53:20	req_72804B494FDF11E7	\N	\N
\.


--
-- Data for Name: service_results; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.service_results (id, service_id, result_hash, input_data, output_data, status, error_message, processed_at, ip_address, user_agent, created_at, updated_at, wallet_transaction_id, user_id) FROM stdin;
\.


--
-- Data for Name: services; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.services (id, title, slug, content, category_id, summary, description, status, featured, author_id, parent_id, views, likes, shares, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, schema, faqs, related_articles, comment_status, icon, published_at, created_at, updated_at, is_paid, cost, currency, price, hidden_fields, short_title, explanation, is_active, keywords, requires_sms) FROM stdin;
20	دریافت تصویر تخلفات رانندگی	traffic-violation-image	<p>test</p>	2	\N	\N	active	f	1	\N	5	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:16:10	2025-09-08 18:49:18	f	5000	IRR	6200	\N	تصویر تخلفات رانندگی	\N	t	\N	f
29	استعلام سوابق بیمه نامه شخص ثالث	third-party-insurance-history	<p>test</p>	10	\N	\N	active	f	1	\N	327	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:33:54	2025-09-08 20:54:17	f	5000	IRR	12500	\N	سوابق بیمه ثالث	\N	t	\N	f
24	لیست پلاک‌های فعال با کدملی	active-plates-list	<p>test</p>	2	\N	\N	active	f	1	\N	4	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:22:42	2025-09-08 18:49:18	f	50000	IRR	90000	\N	پلاک های فعال	\N	t	\N	f
7	استعلام و دریافت شماره شهاب	shahab-number	<p>test</p>	1	\N	\N	active	f	1	\N	15	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	\N	2025-07-08 11:02:51	2025-09-08 20:23:43	f	2500	IRT	5000	\N	استعلام شماره شهاب	\N	t	\N	f
14	استعلام چک در راه	coming-check-inquiry	<p>test</p>	1	\N	\N	active	f	1	\N	4	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 04:36:30	2025-09-08 20:26:51	f	5000	IRR	10000	\N	\N	\N	t	\N	f
35	استعلام اتباع خارجی	expats-inquiries	<p>test</p>	12	\N	\N	active	f	1	\N	4	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:41:11	2025-09-08 20:33:20	f	5000	IRR	10000	\N	اتباع خارجی	\N	t	\N	f
10	استعلام چک برگشتی با کدملی و شناسه صیاد	cheque-inquiry	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	\N	11	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	\N	2025-07-12 04:21:19	2025-09-08 20:52:33	f	6500	IRR	20000	\N	استعلام چک برگشتی	\N	t	\N	f
21	استعلام نمره منفی گواهینامه	negative-license-score	<p>test</p>	2	\N	\N	active	f	1	\N	6	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:18:07	2025-09-08 20:26:19	f	15000	IRR	16179	\N	نمره منفی گواهینامه	\N	t	\N	f
25	استعلام عوارض آزادراهی با شماره پلاک	toll-road-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	4	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:24:06	2025-09-08 18:49:18	f	5000	IRR	10000	\N	عوارض آزادراهی	\N	t	\N	f
3	تبدیل شماره حساب به شبا	account-iban	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	\N	103	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	2025-06-29 06:00:31	2025-06-29 06:00:31	2025-09-08 20:19:02	f	350	IRT	5000	\N	تبدیل حساب به شبا	\N	t	\N	f
23	استعلام تطبیق مالکیت و نوع خودرو	vehicle-ownership-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	6	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:21:03	2025-09-08 20:38:43	f	5000	IRR	10000	\N	تطبیق مالکیت خودرو	\N	t	\N	f
15	استعلام وام و تسهیلات با کدملی	loan-inquiry	<p>test</p>	1	\N	\N	active	f	1	\N	14	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 04:37:41	2025-09-08 20:11:24	f	6500	IRR	12500	\N	استعلام وام	\N	t	\N	f
19	استعلام خلافی موتور	motor-violation-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:14:13	2025-09-08 18:49:19	f	15000	IRR	16170	\N	خلافی موتور	\N	t	\N	f
4	تبدیل شماره شبا به شماره حساب	iban-account	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	\N	20	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	2025-06-29 06:00:33	2025-06-29 06:00:33	2025-09-08 20:19:33	f	350	IRT	5000	\N	تبدیل شبا به حساب	\N	t	\N	f
13	استعلام اعتبار و محکومیت مالی	financial-judgment-inquiry	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	\N	8	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 04:35:03	2025-09-08 19:12:39	f	5000	IRR	10000	\N	استعلام اعتبار مالی	\N	t	\N	f
27	استعلام وضعیت گواهینامه با کدملی	driving-license-status	<p>test</p>	2	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:29:11	2025-09-08 18:49:19	f	5200	IRR	10000	\N	وضعیت گواهینامه	\N	t	\N	f
30	استعلام ریسک رانندگان با کدملی	driver-risk-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	4	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:35:23	2025-09-08 18:49:18	f	5000	IRR	10000	\N	ریسک رانندگان	\N	t	\N	f
17	استعلام کد مکنا با کدملی	inquiry-makna-code	<p>test</p>	1	\N	\N	active	f	1	\N	8	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 04:41:17	2025-09-08 19:28:04	f	5000	IRR	10000	\N	استعلام کد مکنا	\N	t	\N	f
22	استعلام طرح ترافیک خودرو با شماره پلاک	traffic-vehicle-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:19:47	2025-09-08 18:49:19	f	5000	IRR	10000	\N	طرح ترافیک خودرو	\N	t	\N	f
28	تاریخچه پلاک با کدملی و شماره پلاک	plate-history-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:31:02	2025-09-08 18:49:19	f	10000	IRR	25000	\N	تاریخچه پلاک	\N	t	\N	f
6	استعلام رتبه بندی و اعتبارسنجی بانکی	credit-score-rating	<p dir="rtl">ندارد</p>	1	\N	\N	active	t	1	\N	417	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	\N	2025-07-05 10:26:36	2025-09-08 20:54:47	f	8000	IRT	10000	\N	اعتبارسنجی رتبه بانکی	\N	t	\N	f
16	استعلام ضمانت وام با کدملی	loan-guarantee-inquiry	<p>test</p>	1	\N	\N	active	f	1	\N	18	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 04:38:58	2025-09-08 19:48:39	f	5000	IRR	12500	\N	استعلام ضمانت ها	\N	t	\N	f
26	استعلام اطلاعات خودرو و تخفیفات بیمه	car-information-and-insurance-discounts	<p>test</p>	2	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:26:37	2025-09-08 18:49:19	f	2500	IRR	5000	\N	تخفیفات و اطلاعات خودرو	\N	t	\N	f
5	استعلام و بررسی شماره شبا	iban-check	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	\N	25	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	2025-06-29 06:00:37	2025-06-29 06:00:37	2025-09-08 20:19:54	f	350	IRT	5000	\N	بررسی شماره شبا	\N	t	\N	f
11	استعلام رنگ چک با کد ملی	cheque-color	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	\N	6	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	\N	2025-07-12 04:32:31	2025-09-08 20:27:19	f	5000	IRR	10000	\N	استعلام رنگ چک	\N	t	\N	f
37	استعلام سوابق بیمه تامین اجتماعی	social-security-insurance-inquiry	<p>test</p>	10	\N	\N	active	f	1	\N	5	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:46:12	2025-09-08 18:45:14	f	5000	IRR	10000	\N	سوابق تامین اجتماعی	\N	t	\N	f
31	استعلام وضعیت گذرنامه	passport-status-inquiry	<p>test</p>	12	\N	\N	active	f	1	\N	6	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:38:09	2025-09-08 20:20:10	f	5000	IRR	10000	\N	وضعیت گذرنامه	\N	t	\N	f
34	استعلام وضعیت حیات	liveness-inquiry	<p>test</p>	12	\N	\N	active	f	1	\N	3	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:40:07	2025-09-08 18:50:48	f	5000	IRR	10000	\N	وضعیت حیات	\N	t	\N	f
18	استعلام خلافی خودرو کلی و جزئی	car-violation-inquiry	<p>test</p>	2	\N	\N	active	f	1	\N	15	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:13:08	2025-09-08 20:26:12	f	15000	IRR	16170	\N	خلافی خودرو	\N	t	\N	f
36	استعلام وضعیت نظام وظیفه	military-service-status	<p>test</p>	12	\N	\N	active	f	1	\N	6	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:42:11	2025-09-08 20:25:39	f	5000	IRR	10000	\N	وضعیت نظام وظیفه	\N	t	\N	f
40	تبدیل شماره کارت به شماره شبا - بانک گردشگری	card-iban-gardeshgari	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک گردشگری	\N		تبدیل شماره کارت به شماره شبا بانک گردشگری	\N	\N	تبدیل شماره کارت به شماره شبا بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک گردشگری	\N	t	\N	f
39	تبدیل شماره کارت به شماره شبا - بانک اقتصاد نوین	card-iban-eghtesad-novin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک اقتصاد نوین	\N		تبدیل شماره کارت به شماره شبا بانک اقتصاد نوین	\N	\N	تبدیل شماره کارت به شماره شبا بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک اقتصاد نوین	\N	t	\N	f
1	تبدیل شماره کارت به شماره شبا	card-iban	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	\N	200	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	\N	2025-06-28 10:28:57	2025-09-08 20:19:47	f	350	IRT	5000	\N	تبدیل کارت به شبا	\N	t	\N	f
41	تبدیل شماره کارت به شماره شبا - بانک مهر اقتصاد	card-iban-mehr-e-eghtesad	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک مهر اقتصاد	\N		تبدیل شماره کارت به شماره شبا بانک مهر اقتصاد	\N	\N	تبدیل شماره کارت به شماره شبا بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر اقتصاد	\N	t	\N	f
42	تبدیل شماره کارت به شماره شبا - بانک مهر ایران	card-iban-mehr-e-iranian	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک مهر ایران	\N		تبدیل شماره کارت به شماره شبا بانک مهر ایران	\N	\N	تبدیل شماره کارت به شماره شبا بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر ایران	\N	t	\N	f
43	تبدیل شماره کارت به شماره شبا - بانک ملت	card-iban-mellat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک ملت	\N		تبدیل شماره کارت به شماره شبا بانک ملت	\N	\N	تبدیل شماره کارت به شماره شبا بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملت	\N	t	\N	f
44	تبدیل شماره کارت به شماره شبا - بانک ملی	card-iban-melli	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک ملی	\N		تبدیل شماره کارت به شماره شبا بانک ملی	\N	\N	تبدیل شماره کارت به شماره شبا بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملی	\N	t	\N	f
45	تبدیل شماره کارت به شماره شبا - بانک پاسارگاد	card-iban-pasargad	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک پاسارگاد	\N		تبدیل شماره کارت به شماره شبا بانک پاسارگاد	\N	\N	تبدیل شماره کارت به شماره شبا بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پاسارگاد	\N	t	\N	f
46	تبدیل شماره کارت به شماره شبا - بانک رفاه	card-iban-refah	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک رفاه	\N		تبدیل شماره کارت به شماره شبا بانک رفاه	\N	\N	تبدیل شماره کارت به شماره شبا بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رفاه	\N	t	\N	f
47	تبدیل شماره کارت به شماره شبا - بانک رسالت	card-iban-resalat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک رسالت	\N		تبدیل شماره کارت به شماره شبا بانک رسالت	\N	\N	تبدیل شماره کارت به شماره شبا بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رسالت	\N	t	\N	f
48	تبدیل شماره کارت به شماره شبا - بانک صادرات	card-iban-saderat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک صادرات	\N		تبدیل شماره کارت به شماره شبا بانک صادرات	\N	\N	تبدیل شماره کارت به شماره شبا بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک صادرات	\N	t	\N	f
49	تبدیل شماره کارت به شماره شبا - بانک سپه	card-iban-sepah	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک سپه	\N		تبدیل شماره کارت به شماره شبا بانک سپه	\N	\N	تبدیل شماره کارت به شماره شبا بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سپه	\N	t	\N	f
50	تبدیل شماره کارت به شماره شبا - بانک شهر	card-iban-shahr	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک شهر	\N		تبدیل شماره کارت به شماره شبا بانک شهر	\N	\N	تبدیل شماره کارت به شماره شبا بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک شهر	\N	t	\N	f
33	استعلام ممنوع الخروجی	inquiry-exit-ban	<p>test</p>	12	\N	\N	active	f	1	\N	7	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-07-12 05:39:02	2025-09-08 18:50:48	f	5000	IRR	10000	\N	ممنوع الخروجی	\N	t	\N	f
54	تبدیل شماره کارت به شماره حساب - بانک اقتصاد نوین	card-account-eghtesad-novin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک اقتصاد نوین	\N		تبدیل شماره کارت به شماره حساب بانک اقتصاد نوین	\N	\N	تبدیل شماره کارت به شماره حساب بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک اقتصاد نوین	\N	t	\N	f
55	تبدیل شماره کارت به شماره حساب - بانک گردشگری	card-account-gardeshgari	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک گردشگری	\N		تبدیل شماره کارت به شماره حساب بانک گردشگری	\N	\N	تبدیل شماره کارت به شماره حساب بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک گردشگری	\N	t	\N	f
56	تبدیل شماره کارت به شماره حساب - بانک مهر ایران	card-account-mehr-e-iranian	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک مهر ایران	\N		تبدیل شماره کارت به شماره حساب بانک مهر ایران	\N	\N	تبدیل شماره کارت به شماره حساب بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر ایران	\N	t	\N	f
57	تبدیل شماره کارت به شماره حساب - بانک ملل	card-account-melal	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک ملل	\N		تبدیل شماره کارت به شماره حساب بانک ملل	\N	\N	تبدیل شماره کارت به شماره حساب بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملل	\N	t	\N	f
58	تبدیل شماره کارت به شماره حساب - بانک رفاه	card-account-refah	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک رفاه	\N		تبدیل شماره کارت به شماره حساب بانک رفاه	\N	\N	تبدیل شماره کارت به شماره حساب بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رفاه	\N	t	\N	f
59	تبدیل شماره کارت به شماره حساب - بانک صادرات	card-account-saderat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک صادرات	\N		تبدیل شماره کارت به شماره حساب بانک صادرات	\N	\N	تبدیل شماره کارت به شماره حساب بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک صادرات	\N	t	\N	f
60	تبدیل شماره کارت به شماره حساب - بانک سپه	card-account-sepah	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک سپه	\N		تبدیل شماره کارت به شماره حساب بانک سپه	\N	\N	تبدیل شماره کارت به شماره حساب بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سپه	\N	t	\N	f
61	تبدیل شماره کارت به شماره حساب - بانک تجارت	card-account-tejarat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک تجارت	\N		تبدیل شماره کارت به شماره حساب بانک تجارت	\N	\N	تبدیل شماره کارت به شماره حساب بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک تجارت	\N	t	\N	f
62	تبدیل شماره شبا به شماره حساب - بانک اقتصاد نوین	iban-account-eghtesad-novin	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک اقتصاد نوین	\N		تبدیل شماره شبا به شماره حساب بانک اقتصاد نوین	\N	\N	تبدیل شماره شبا به شماره حساب بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک اقتصاد نوین	\N	t	\N	f
63	تبدیل شماره شبا به شماره حساب - بانک گردشگری	iban-account-gardeshgari	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک گردشگری	\N		تبدیل شماره شبا به شماره حساب بانک گردشگری	\N	\N	تبدیل شماره شبا به شماره حساب بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک گردشگری	\N	t	\N	f
64	تبدیل شماره شبا به شماره حساب - بانک ایران زمین	iban-account-iranzamin	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک ایران زمین	\N		تبدیل شماره شبا به شماره حساب بانک ایران زمین	\N	\N	تبدیل شماره شبا به شماره حساب بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ایران زمین	\N	t	\N	f
53	تبدیل شماره کارت به شماره شبا - بانک توسعه تعاون	card-iban-tosee-taavon	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک توسعه تعاون	\N		تبدیل شماره کارت به شماره شبا بانک توسعه تعاون	\N	\N	تبدیل شماره کارت به شماره شبا بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک توسعه تعاون	\N	t	\N	f
67	تبدیل شماره شبا به شماره حساب - بانک ملل	iban-account-melal	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک ملل	\N		تبدیل شماره شبا به شماره حساب بانک ملل	\N	\N	تبدیل شماره شبا به شماره حساب بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملل	\N	t	\N	f
68	تبدیل شماره شبا به شماره حساب - بانک ملت	iban-account-mellat	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک ملت	\N		تبدیل شماره شبا به شماره حساب بانک ملت	\N	\N	تبدیل شماره شبا به شماره حساب بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملت	\N	t	\N	f
69	تبدیل شماره شبا به شماره حساب - بانک پارسیان	iban-account-parsian	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک پارسیان	\N		تبدیل شماره شبا به شماره حساب بانک پارسیان	\N	\N	تبدیل شماره شبا به شماره حساب بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پارسیان	\N	t	\N	f
70	تبدیل شماره شبا به شماره حساب - پست بانک	iban-account-post-bank	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب پست بانک	\N		تبدیل شماره شبا به شماره حساب پست بانک	\N	\N	تبدیل شماره شبا به شماره حساب پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	پست بانک	\N	t	\N	f
71	تبدیل شماره شبا به شماره حساب - بانک رسالت	iban-account-resalat	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک رسالت	\N		تبدیل شماره شبا به شماره حساب بانک رسالت	\N	\N	تبدیل شماره شبا به شماره حساب بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رسالت	\N	t	\N	f
72	تبدیل شماره شبا به شماره حساب - بانک صادرات	iban-account-saderat	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک صادرات	\N		تبدیل شماره شبا به شماره حساب بانک صادرات	\N	\N	تبدیل شماره شبا به شماره حساب بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک صادرات	\N	t	\N	f
73	تبدیل شماره شبا به شماره حساب - بانک سامان	iban-account-saman	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک سامان	\N		تبدیل شماره شبا به شماره حساب بانک سامان	\N	\N	تبدیل شماره شبا به شماره حساب بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سامان	\N	t	\N	f
74	تبدیل شماره شبا به شماره حساب - بانک سپه	iban-account-sepah	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک سپه	\N		تبدیل شماره شبا به شماره حساب بانک سپه	\N	\N	تبدیل شماره شبا به شماره حساب بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سپه	\N	t	\N	f
75	تبدیل شماره شبا به شماره حساب - بانک تجارت	iban-account-tejarat	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک تجارت	\N		تبدیل شماره شبا به شماره حساب بانک تجارت	\N	\N	تبدیل شماره شبا به شماره حساب بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک تجارت	\N	t	\N	f
76	تبدیل شماره شبا به شماره حساب - بانک توسعه تعاون	iban-account-tosee-taavon	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک توسعه تعاون	\N		تبدیل شماره شبا به شماره حساب بانک توسعه تعاون	\N	\N	تبدیل شماره شبا به شماره حساب بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک توسعه تعاون	\N	t	\N	f
77	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک گردشگری	credit-score-rating-gardeshgari	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک گردشگری	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک گردشگری	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک گردشگری	\N	t	\N	f
78	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک ایران زمین	credit-score-rating-iranzamin	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ایران زمین	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک ایران زمین	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک ایران زمین	\N	t	\N	f
66	تبدیل شماره شبا به شماره حساب - بانک مهر ایران	iban-account-mehr-e-iranian	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک مهر ایران	\N		تبدیل شماره شبا به شماره حساب بانک مهر ایران	\N	\N	تبدیل شماره شبا به شماره حساب بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر ایران	\N	t	\N	f
81	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک مسکن	credit-score-rating-maskan	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مسکن	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک مسکن	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک مسکن	\N	t	\N	f
82	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک مهر ایران	credit-score-rating-mehr-e-iranian	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر ایران	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر ایران	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک مهر ایران	\N	t	\N	f
83	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک ملل	credit-score-rating-melal	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملل	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملل	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک ملل	\N	t	\N	f
85	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک ملی	credit-score-rating-melli	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملی	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملی	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک ملی	\N	t	\N	f
86	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک پارسیان	credit-score-rating-parsian	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک پارسیان	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک پارسیان	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک پارسیان	\N	t	\N	f
87	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک پاسارگاد	credit-score-rating-pasargad	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک پاسارگاد	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک پاسارگاد	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک پاسارگاد	\N	t	\N	f
88	استعلام رتبه بندی و اعتبارسنجی بانکی - پست بانک	credit-score-rating-post-bank	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی پست بانک	\N		استعلام رتبه بندی و اعتبارسنجی بانکی پست بانک	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	پست بانک	\N	t	\N	f
89	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک رفاه	credit-score-rating-refah	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک رفاه	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک رفاه	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک رفاه	\N	t	\N	f
90	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک صادرات	credit-score-rating-saderat	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک صادرات	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک صادرات	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک صادرات	\N	t	\N	f
91	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک سامان	credit-score-rating-saman	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سامان	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک سامان	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک سامان	\N	t	\N	f
122	استعلام رنگ چک با کد ملی - بانک خاورمیانه	check-color-khavarmianeh	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک خاورمیانه	\N		استعلام رنگ چک با کد ملی بانک خاورمیانه	\N	\N	استعلام رنگ چک با کد ملی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک خاورمیانه	\N	t	\N	f
84	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک ملت	credit-score-rating-mellat	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	3	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملت	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملت	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 20:28:41	f	8000	IRT	10000	\N	بانک ملت	\N	t	\N	f
94	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک سینا	credit-score-rating-sina	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سینا	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک سینا	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک سینا	\N	t	\N	f
95	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک تجارت	credit-score-rating-tejarat	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک تجارت	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک تجارت	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک تجارت	\N	t	\N	f
97	استعلام وام و تسهیلات با کدملی - بانک کشاورزی	loan-inquiry-keshavarzi	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک کشاورزی	\N		استعلام وام و تسهیلات با کدملی بانک کشاورزی	\N	\N	استعلام وام و تسهیلات با کدملی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک کشاورزی	\N	t	\N	f
98	استعلام وام و تسهیلات با کدملی - بانک مسکن	loan-inquiry-maskan	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک مسکن	\N		استعلام وام و تسهیلات با کدملی بانک مسکن	\N	\N	استعلام وام و تسهیلات با کدملی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک مسکن	\N	t	\N	f
99	استعلام وام و تسهیلات با کدملی - بانک مهر ایران	loan-inquiry-mehr-e-iranian	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک مهر ایران	\N		استعلام وام و تسهیلات با کدملی بانک مهر ایران	\N	\N	استعلام وام و تسهیلات با کدملی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک مهر ایران	\N	t	\N	f
100	استعلام وام و تسهیلات با کدملی - بانک ملت	loan-inquiry-mellat	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک ملت	\N		استعلام وام و تسهیلات با کدملی بانک ملت	\N	\N	استعلام وام و تسهیلات با کدملی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک ملت	\N	t	\N	f
101	استعلام وام و تسهیلات با کدملی - بانک پارسیان	loan-inquiry-parsian	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک پارسیان	\N		استعلام وام و تسهیلات با کدملی بانک پارسیان	\N	\N	استعلام وام و تسهیلات با کدملی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک پارسیان	\N	t	\N	f
102	استعلام وام و تسهیلات با کدملی - بانک رفاه	loan-inquiry-refah	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک رفاه	\N		استعلام وام و تسهیلات با کدملی بانک رفاه	\N	\N	استعلام وام و تسهیلات با کدملی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک رفاه	\N	t	\N	f
103	استعلام وام و تسهیلات با کدملی - بانک رسالت	loan-inquiry-resalat	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک رسالت	\N		استعلام وام و تسهیلات با کدملی بانک رسالت	\N	\N	استعلام وام و تسهیلات با کدملی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک رسالت	\N	t	\N	f
104	استعلام وام و تسهیلات با کدملی - بانک سپه	loan-inquiry-sepah	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک سپه	\N		استعلام وام و تسهیلات با کدملی بانک سپه	\N	\N	استعلام وام و تسهیلات با کدملی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک سپه	\N	t	\N	f
105	استعلام وام و تسهیلات با کدملی - بانک سینا	loan-inquiry-sina	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک سینا	\N		استعلام وام و تسهیلات با کدملی بانک سینا	\N	\N	استعلام وام و تسهیلات با کدملی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک سینا	\N	t	\N	f
106	استعلام وام و تسهیلات با کدملی - بانک تجارت	loan-inquiry-tejarat	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک تجارت	\N		استعلام وام و تسهیلات با کدملی بانک تجارت	\N	\N	استعلام وام و تسهیلات با کدملی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک تجارت	\N	t	\N	f
93	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک شهر	credit-score-rating-shahr	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک شهر	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک شهر	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک شهر	\N	t	\N	f
109	تبدیل شماره حساب به شبا - بانک مسکن	account-iban-maskan	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک مسکن	\N		تبدیل شماره حساب به شبا بانک مسکن	\N	\N	تبدیل شماره حساب به شبا بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مسکن	\N	t	\N	f
110	تبدیل شماره حساب به شبا - بانک ملت	account-iban-mellat	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک ملت	\N		تبدیل شماره حساب به شبا بانک ملت	\N	\N	تبدیل شماره حساب به شبا بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملت	\N	t	\N	f
111	تبدیل شماره حساب به شبا - بانک ملی	account-iban-melli	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک ملی	\N		تبدیل شماره حساب به شبا بانک ملی	\N	\N	تبدیل شماره حساب به شبا بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملی	\N	t	\N	f
113	تبدیل شماره حساب به شبا - بانک رسالت	account-iban-resalat	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک رسالت	\N		تبدیل شماره حساب به شبا بانک رسالت	\N	\N	تبدیل شماره حساب به شبا بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رسالت	\N	t	\N	f
114	تبدیل شماره حساب به شبا - بانک سپه	account-iban-sepah	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک سپه	\N		تبدیل شماره حساب به شبا بانک سپه	\N	\N	تبدیل شماره حساب به شبا بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سپه	\N	t	\N	f
115	استعلام و بررسی شماره شبا - بانک تجارت	iban-check-tejarat	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک تجارت	\N		استعلام و بررسی شماره شبا بانک تجارت	\N	\N	استعلام و بررسی شماره شبا بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک تجارت	\N	t	\N	f
116	استعلام رنگ چک با کد ملی - بانک دی	check-color-dey	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک دی	\N		استعلام رنگ چک با کد ملی بانک دی	\N	\N	استعلام رنگ چک با کد ملی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک دی	\N	t	\N	f
117	استعلام رنگ چک با کد ملی - بانک اقتصاد نوین	check-color-eghtesad-novin	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک اقتصاد نوین	\N		استعلام رنگ چک با کد ملی بانک اقتصاد نوین	\N	\N	استعلام رنگ چک با کد ملی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک اقتصاد نوین	\N	t	\N	f
118	استعلام رنگ چک با کد ملی - بانک گردشگری	check-color-gardeshgari	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک گردشگری	\N		استعلام رنگ چک با کد ملی بانک گردشگری	\N	\N	استعلام رنگ چک با کد ملی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک گردشگری	\N	t	\N	f
119	استعلام رنگ چک با کد ملی - بانک ایران زمین	check-color-iranzamin	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک ایران زمین	\N		استعلام رنگ چک با کد ملی بانک ایران زمین	\N	\N	استعلام رنگ چک با کد ملی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ایران زمین	\N	t	\N	f
120	استعلام رنگ چک با کد ملی - بانک کارآفرین	check-color-karafarin	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک کارآفرین	\N		استعلام رنگ چک با کد ملی بانک کارآفرین	\N	\N	استعلام رنگ چک با کد ملی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک کارآفرین	\N	t	\N	f
121	استعلام رنگ چک با کد ملی - بانک کشاورزی	check-color-keshavarzi	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک کشاورزی	\N		استعلام رنگ چک با کد ملی بانک کشاورزی	\N	\N	استعلام رنگ چک با کد ملی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک کشاورزی	\N	t	\N	f
185	استعلام چک در راه - بانک سامان	coming-check-inquiry-saman	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک سامان	\N		استعلام چک در راه بانک سامان	\N	\N	استعلام چک در راه بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سامان	\N	t	\N	f
108	تبدیل شماره حساب به شبا - بانک کارآفرین	account-iban-karafarin	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک کارآفرین	\N		تبدیل شماره حساب به شبا بانک کارآفرین	\N	\N	تبدیل شماره حساب به شبا بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کارآفرین	\N	t	\N	f
126	استعلام رنگ چک با کد ملی - بانک ملل	check-color-melal	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک ملل	\N		استعلام رنگ چک با کد ملی بانک ملل	\N	\N	استعلام رنگ چک با کد ملی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ملل	\N	t	\N	f
127	استعلام رنگ چک با کد ملی - بانک ملت	check-color-mellat	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک ملت	\N		استعلام رنگ چک با کد ملی بانک ملت	\N	\N	استعلام رنگ چک با کد ملی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ملت	\N	t	\N	f
128	استعلام رنگ چک با کد ملی - بانک ملی	check-color-melli	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک ملی	\N		استعلام رنگ چک با کد ملی بانک ملی	\N	\N	استعلام رنگ چک با کد ملی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ملی	\N	t	\N	f
129	استعلام رنگ چک با کد ملی - بانک پارسیان	check-color-parsian	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک پارسیان	\N		استعلام رنگ چک با کد ملی بانک پارسیان	\N	\N	استعلام رنگ چک با کد ملی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک پارسیان	\N	t	\N	f
130	استعلام رنگ چک با کد ملی - بانک پاسارگاد	check-color-pasargad	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک پاسارگاد	\N		استعلام رنگ چک با کد ملی بانک پاسارگاد	\N	\N	استعلام رنگ چک با کد ملی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک پاسارگاد	\N	t	\N	f
131	استعلام رنگ چک با کد ملی - پست بانک	check-color-post-bank	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی پست بانک	\N		استعلام رنگ چک با کد ملی پست بانک	\N	\N	استعلام رنگ چک با کد ملی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	پست بانک	\N	t	\N	f
132	استعلام رنگ چک با کد ملی - بانک رفاه	check-color-refah	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک رفاه	\N		استعلام رنگ چک با کد ملی بانک رفاه	\N	\N	استعلام رنگ چک با کد ملی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک رفاه	\N	t	\N	f
133	استعلام رنگ چک با کد ملی - بانک رسالت	check-color-resalat	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک رسالت	\N		استعلام رنگ چک با کد ملی بانک رسالت	\N	\N	استعلام رنگ چک با کد ملی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک رسالت	\N	t	\N	f
134	استعلام رنگ چک با کد ملی - بانک صادرات	check-color-saderat	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک صادرات	\N		استعلام رنگ چک با کد ملی بانک صادرات	\N	\N	استعلام رنگ چک با کد ملی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک صادرات	\N	t	\N	f
135	استعلام رنگ چک با کد ملی - بانک سامان	check-color-saman	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک سامان	\N		استعلام رنگ چک با کد ملی بانک سامان	\N	\N	استعلام رنگ چک با کد ملی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سامان	\N	t	\N	f
136	استعلام رنگ چک با کد ملی - بانک سپه	check-color-sepah	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک سپه	\N		استعلام رنگ چک با کد ملی بانک سپه	\N	\N	استعلام رنگ چک با کد ملی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سپه	\N	t	\N	f
137	استعلام رنگ چک با کد ملی - بانک شهر	check-color-shahr	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک شهر	\N		استعلام رنگ چک با کد ملی بانک شهر	\N	\N	استعلام رنگ چک با کد ملی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک شهر	\N	t	\N	f
138	استعلام رنگ چک با کد ملی - بانک سینا	check-color-sina	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک سینا	\N		استعلام رنگ چک با کد ملی بانک سینا	\N	\N	استعلام رنگ چک با کد ملی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سینا	\N	t	\N	f
139	استعلام رنگ چک با کد ملی - بانک تجارت	check-color-tejarat	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک تجارت	\N		استعلام رنگ چک با کد ملی بانک تجارت	\N	\N	استعلام رنگ چک با کد ملی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک تجارت	\N	t	\N	f
125	استعلام رنگ چک با کد ملی - بانک مهر ایران	check-color-mehr-e-iranian	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک مهر ایران	\N		استعلام رنگ چک با کد ملی بانک مهر ایران	\N	\N	استعلام رنگ چک با کد ملی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک مهر ایران	\N	t	\N	f
142	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک اقتصاد نوین	cheque-inquiery-eghtesad-novin	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک اقتصاد نوین	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک اقتصاد نوین	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک اقتصاد نوین	\N	t	\N	f
143	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک گردشگری	cheque-inquiery-gardeshgari	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک گردشگری	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک گردشگری	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک گردشگری	\N	t	\N	f
144	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک ایران زمین	cheque-inquiery-iranzamin	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ایران زمین	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک ایران زمین	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک ایران زمین	\N	t	\N	f
145	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک کارآفرین	cheque-inquiery-karafarin	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک کارآفرین	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک کارآفرین	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک کارآفرین	\N	t	\N	f
146	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک کشاورزی	cheque-inquiery-keshavarzi	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک کشاورزی	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک کشاورزی	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک کشاورزی	\N	t	\N	f
147	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک خاورمیانه	cheque-inquiery-khavarmianeh	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک خاورمیانه	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک خاورمیانه	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک خاورمیانه	\N	t	\N	f
148	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک مسکن	cheque-inquiery-maskan	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مسکن	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک مسکن	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک مسکن	\N	t	\N	f
149	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک مهر اقتصاد	cheque-inquiery-mehr-e-eghtesad	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر اقتصاد	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر اقتصاد	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک مهر اقتصاد	\N	t	\N	f
150	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک مهر ایران	cheque-inquiery-mehr-e-iranian	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر ایران	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر ایران	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک مهر ایران	\N	t	\N	f
151	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک ملل	cheque-inquiery-melal	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملل	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملل	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک ملل	\N	t	\N	f
152	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک ملت	cheque-inquiery-mellat	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملت	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملت	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک ملت	\N	t	\N	f
141	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک دی	cheque-inquiery-dey	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک دی	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک دی	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک دی	\N	t	\N	f
155	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک پاسارگاد	cheque-inquiery-pasargad	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک پاسارگاد	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک پاسارگاد	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک پاسارگاد	\N	t	\N	f
156	استعلام چک برگشتی با کدملی و شناسه صیاد - پست بانک	cheque-inquiery-post-bank	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد پست بانک	\N		استعلام چک برگشتی با کدملی و شناسه صیاد پست بانک	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	پست بانک	\N	t	\N	f
157	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک رفاه	cheque-inquiery-refah	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک رفاه	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک رفاه	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک رفاه	\N	t	\N	f
158	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک رسالت	cheque-inquiery-resalat	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک رسالت	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک رسالت	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک رسالت	\N	t	\N	f
160	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک سامان	cheque-inquiery-saman	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سامان	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک سامان	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک سامان	\N	t	\N	f
161	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک سپه	cheque-inquiery-sepah	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سپه	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک سپه	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک سپه	\N	t	\N	f
162	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک شهر	cheque-inquiery-shahr	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک شهر	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک شهر	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک شهر	\N	t	\N	f
163	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک سینا	cheque-inquiery-sina	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سینا	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک سینا	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک سینا	\N	t	\N	f
165	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک توسعه تعاون	cheque-inquiery-tosee-taavon	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک توسعه تعاون	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک توسعه تعاون	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک توسعه تعاون	\N	t	\N	f
166	استعلام چک در راه - بانک دی	coming-check-inquiry-dey	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک دی	\N		استعلام چک در راه بانک دی	\N	\N	استعلام چک در راه بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک دی	\N	t	\N	f
159	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک صادرات	cheque-inquiery-saderat	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	1	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک صادرات	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک صادرات	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 20:28:37	f	6500	IRR	12500	\N	بانک صادرات	\N	t	\N	f
154	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک پارسیان	cheque-inquiery-parsian	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک پارسیان	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک پارسیان	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	6500	IRR	12500	\N	بانک پارسیان	\N	t	\N	f
169	استعلام چک در راه - بانک ایران زمین	coming-check-inquiry-iranzamin	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک ایران زمین	\N		استعلام چک در راه بانک ایران زمین	\N	\N	استعلام چک در راه بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ایران زمین	\N	t	\N	f
170	استعلام چک در راه - بانک کارآفرین	coming-check-inquiry-karafarin	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک کارآفرین	\N		استعلام چک در راه بانک کارآفرین	\N	\N	استعلام چک در راه بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک کارآفرین	\N	t	\N	f
171	استعلام چک در راه - بانک کشاورزی	coming-check-inquiry-keshavarzi	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک کشاورزی	\N		استعلام چک در راه بانک کشاورزی	\N	\N	استعلام چک در راه بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک کشاورزی	\N	t	\N	f
172	استعلام چک در راه - بانک خاورمیانه	coming-check-inquiry-khavarmianeh	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک خاورمیانه	\N		استعلام چک در راه بانک خاورمیانه	\N	\N	استعلام چک در راه بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک خاورمیانه	\N	t	\N	f
173	استعلام چک در راه - بانک مسکن	coming-check-inquiry-maskan	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک مسکن	\N		استعلام چک در راه بانک مسکن	\N	\N	استعلام چک در راه بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک مسکن	\N	t	\N	f
174	استعلام چک در راه - بانک مهر اقتصاد	coming-check-inquiry-mehr-e-eghtesad	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک مهر اقتصاد	\N		استعلام چک در راه بانک مهر اقتصاد	\N	\N	استعلام چک در راه بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک مهر اقتصاد	\N	t	\N	f
175	استعلام چک در راه - بانک مهر ایران	coming-check-inquiry-mehr-e-iranian	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک مهر ایران	\N		استعلام چک در راه بانک مهر ایران	\N	\N	استعلام چک در راه بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک مهر ایران	\N	t	\N	f
176	استعلام چک در راه - بانک ملل	coming-check-inquiry-melal	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک ملل	\N		استعلام چک در راه بانک ملل	\N	\N	استعلام چک در راه بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ملل	\N	t	\N	f
177	استعلام چک در راه - بانک ملت	coming-check-inquiry-mellat	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک ملت	\N		استعلام چک در راه بانک ملت	\N	\N	استعلام چک در راه بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک ملت	\N	t	\N	f
179	استعلام چک در راه - بانک پارسیان	coming-check-inquiry-parsian	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک پارسیان	\N		استعلام چک در راه بانک پارسیان	\N	\N	استعلام چک در راه بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک پارسیان	\N	t	\N	f
180	استعلام چک در راه - بانک پاسارگاد	coming-check-inquiry-pasargad	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک پاسارگاد	\N		استعلام چک در راه بانک پاسارگاد	\N	\N	استعلام چک در راه بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک پاسارگاد	\N	t	\N	f
181	استعلام چک در راه - پست بانک	coming-check-inquiry-post-bank	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه پست بانک	\N		استعلام چک در راه پست بانک	\N	\N	استعلام چک در راه پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	پست بانک	\N	t	\N	f
182	استعلام چک در راه - بانک رفاه	coming-check-inquiry-refah	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک رفاه	\N		استعلام چک در راه بانک رفاه	\N	\N	استعلام چک در راه بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک رفاه	\N	t	\N	f
183	استعلام چک در راه - بانک رسالت	coming-check-inquiry-resalat	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک رسالت	\N		استعلام چک در راه بانک رسالت	\N	\N	استعلام چک در راه بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک رسالت	\N	t	\N	f
184	استعلام چک در راه - بانک صادرات	coming-check-inquiry-saderat	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک صادرات	\N		استعلام چک در راه بانک صادرات	\N	\N	استعلام چک در راه بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک صادرات	\N	t	\N	f
178	استعلام چک در راه - بانک ملی	coming-check-inquiry-melli	<p>test</p>	1	\N	\N	active	f	1	14	1	0	0	استعلام چک در راه بانک ملی	\N		استعلام چک در راه بانک ملی	\N	\N	استعلام چک در راه بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 20:28:44	f	5000	IRR	10000	\N	بانک ملی	\N	t	\N	f
168	استعلام چک در راه - بانک گردشگری	coming-check-inquiry-gardeshgari	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک گردشگری	\N		استعلام چک در راه بانک گردشگری	\N	\N	استعلام چک در راه بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک گردشگری	\N	t	\N	f
187	استعلام چک در راه - بانک شهر	coming-check-inquiry-shahr	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک شهر	\N		استعلام چک در راه بانک شهر	\N	\N	استعلام چک در راه بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک شهر	\N	t	\N	f
188	استعلام چک در راه - بانک سینا	coming-check-inquiry-sina	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک سینا	\N		استعلام چک در راه بانک سینا	\N	\N	استعلام چک در راه بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سینا	\N	t	\N	f
189	استعلام چک در راه - بانک تجارت	coming-check-inquiry-tejarat	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک تجارت	\N		استعلام چک در راه بانک تجارت	\N	\N	استعلام چک در راه بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک تجارت	\N	t	\N	f
190	استعلام چک در راه - بانک توسعه تعاون	coming-check-inquiry-tosee-taavon	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک توسعه تعاون	\N		استعلام چک در راه بانک توسعه تعاون	\N	\N	استعلام چک در راه بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک توسعه تعاون	\N	t	\N	f
191	تبدیل شماره کارت به شماره شبا - بانک ایران زمین	card-iban-iranzamin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک ایران زمین	\N		تبدیل شماره کارت به شماره شبا بانک ایران زمین	\N	\N	تبدیل شماره کارت به شماره شبا بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ایران زمین	\N	t	\N	f
192	تبدیل شماره کارت به شماره شبا - بانک کارآفرین	card-iban-karafarin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک کارآفرین	\N		تبدیل شماره کارت به شماره شبا بانک کارآفرین	\N	\N	تبدیل شماره کارت به شماره شبا بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کارآفرین	\N	t	\N	f
193	تبدیل شماره کارت به شماره شبا - بانک کشاورزی	card-iban-keshavarzi	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک کشاورزی	\N		تبدیل شماره کارت به شماره شبا بانک کشاورزی	\N	\N	تبدیل شماره کارت به شماره شبا بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کشاورزی	\N	t	\N	f
194	تبدیل شماره کارت به شماره شبا - بانک خاورمیانه	card-iban-khavarmianeh	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک خاورمیانه	\N		تبدیل شماره کارت به شماره شبا بانک خاورمیانه	\N	\N	تبدیل شماره کارت به شماره شبا بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک خاورمیانه	\N	t	\N	f
196	تبدیل شماره کارت به شماره شبا - بانک ملل	card-iban-melal	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک ملل	\N		تبدیل شماره کارت به شماره شبا بانک ملل	\N	\N	تبدیل شماره کارت به شماره شبا بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملل	\N	t	\N	f
198	تبدیل شماره کارت به شماره شبا - پست بانک	card-iban-post-bank	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا پست بانک	\N		تبدیل شماره کارت به شماره شبا پست بانک	\N	\N	تبدیل شماره کارت به شماره شبا پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	350	IRT	2500	\N	پست بانک	\N	t	\N	f
199	تبدیل شماره کارت به شماره شبا - بانک سامان	card-iban-saman	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک سامان	\N		تبدیل شماره کارت به شماره شبا بانک سامان	\N	\N	تبدیل شماره کارت به شماره شبا بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سامان	\N	t	\N	f
195	تبدیل شماره کارت به شماره شبا - بانک مسکن	card-iban-maskan	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	1	0	0	تبدیل شماره کارت به شماره شبا بانک مسکن	\N		تبدیل شماره کارت به شماره شبا بانک مسکن	\N	\N	تبدیل شماره کارت به شماره شبا بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 20:35:54	f	350	IRT	2500	\N	بانک مسکن	\N	t	\N	f
200	تبدیل شماره کارت به شماره حساب - بانک دی	card-account-dey	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	1	0	0	تبدیل شماره کارت به شماره حساب بانک دی	\N		تبدیل شماره کارت به شماره حساب بانک دی	\N	\N	تبدیل شماره کارت به شماره حساب بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:28:23	f	350	IRT	2500	\N	بانک دی	\N	t	\N	f
186	استعلام چک در راه - بانک سپه	coming-check-inquiry-sepah	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک سپه	\N		استعلام چک در راه بانک سپه	\N	\N	استعلام چک در راه بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:19	f	5000	IRR	10000	\N	بانک سپه	\N	t	\N	f
202	تبدیل شماره کارت به شماره حساب - بانک کارآفرین	card-account-karafarin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک کارآفرین	\N		تبدیل شماره کارت به شماره حساب بانک کارآفرین	\N	\N	تبدیل شماره کارت به شماره حساب بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کارآفرین	\N	t	\N	f
203	تبدیل شماره کارت به شماره حساب - بانک کشاورزی	card-account-keshavarzi	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک کشاورزی	\N		تبدیل شماره کارت به شماره حساب بانک کشاورزی	\N	\N	تبدیل شماره کارت به شماره حساب بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کشاورزی	\N	t	\N	f
205	تبدیل شماره کارت به شماره حساب - بانک مسکن	card-account-maskan	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک مسکن	\N		تبدیل شماره کارت به شماره حساب بانک مسکن	\N	\N	تبدیل شماره کارت به شماره حساب بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مسکن	\N	t	\N	f
206	تبدیل شماره کارت به شماره حساب - بانک مهر اقتصاد	card-account-mehr-e-eghtesad	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک مهر اقتصاد	\N		تبدیل شماره کارت به شماره حساب بانک مهر اقتصاد	\N	\N	تبدیل شماره کارت به شماره حساب بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر اقتصاد	\N	t	\N	f
207	تبدیل شماره کارت به شماره حساب - بانک ملت	card-account-mellat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک ملت	\N		تبدیل شماره کارت به شماره حساب بانک ملت	\N	\N	تبدیل شماره کارت به شماره حساب بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملت	\N	t	\N	f
208	تبدیل شماره کارت به شماره حساب - بانک ملی	card-account-melli	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک ملی	\N		تبدیل شماره کارت به شماره حساب بانک ملی	\N	\N	تبدیل شماره کارت به شماره حساب بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملی	\N	t	\N	f
209	تبدیل شماره کارت به شماره حساب - بانک پارسیان	card-account-parsian	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک پارسیان	\N		تبدیل شماره کارت به شماره حساب بانک پارسیان	\N	\N	تبدیل شماره کارت به شماره حساب بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پارسیان	\N	t	\N	f
211	تبدیل شماره کارت به شماره حساب - پست بانک	card-account-post-bank	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب پست بانک	\N		تبدیل شماره کارت به شماره حساب پست بانک	\N	\N	تبدیل شماره کارت به شماره حساب پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	پست بانک	\N	t	\N	f
212	تبدیل شماره کارت به شماره حساب - بانک رسالت	card-account-resalat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک رسالت	\N		تبدیل شماره کارت به شماره حساب بانک رسالت	\N	\N	تبدیل شماره کارت به شماره حساب بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رسالت	\N	t	\N	f
213	تبدیل شماره کارت به شماره حساب - بانک سامان	card-account-saman	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک سامان	\N		تبدیل شماره کارت به شماره حساب بانک سامان	\N	\N	تبدیل شماره کارت به شماره حساب بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سامان	\N	t	\N	f
214	تبدیل شماره کارت به شماره حساب - بانک شهر	card-account-shahr	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک شهر	\N		تبدیل شماره کارت به شماره حساب بانک شهر	\N	\N	تبدیل شماره کارت به شماره حساب بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک شهر	\N	t	\N	f
210	تبدیل شماره کارت به شماره حساب - بانک پاسارگاد	card-account-pasargad	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	1	0	0	تبدیل شماره کارت به شماره حساب بانک پاسارگاد	\N		تبدیل شماره کارت به شماره حساب بانک پاسارگاد	\N	\N	تبدیل شماره کارت به شماره حساب بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:35:56	f	350	IRT	2500	\N	بانک پاسارگاد	\N	t	\N	f
218	تبدیل شماره حساب به شبا - بانک اقتصاد نوین	account-iban-eghtesad-novin	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک اقتصاد نوین	\N		تبدیل شماره حساب به شبا بانک اقتصاد نوین	\N	\N	تبدیل شماره حساب به شبا بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک اقتصاد نوین	\N	t	\N	f
219	تبدیل شماره حساب به شبا - بانک گردشگری	account-iban-gardeshgari	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک گردشگری	\N		تبدیل شماره حساب به شبا بانک گردشگری	\N	\N	تبدیل شماره حساب به شبا بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک گردشگری	\N	t	\N	f
220	تبدیل شماره حساب به شبا - بانک ایران زمین	account-iban-iranzamin	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک ایران زمین	\N		تبدیل شماره حساب به شبا بانک ایران زمین	\N	\N	تبدیل شماره حساب به شبا بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ایران زمین	\N	t	\N	f
221	تبدیل شماره حساب به شبا - بانک کشاورزی	account-iban-keshavarzi	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک کشاورزی	\N		تبدیل شماره حساب به شبا بانک کشاورزی	\N	\N	تبدیل شماره حساب به شبا بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کشاورزی	\N	t	\N	f
222	تبدیل شماره حساب به شبا - بانک خاورمیانه	account-iban-khavarmianeh	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک خاورمیانه	\N		تبدیل شماره حساب به شبا بانک خاورمیانه	\N	\N	تبدیل شماره حساب به شبا بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک خاورمیانه	\N	t	\N	f
223	تبدیل شماره حساب به شبا - بانک مهر اقتصاد	account-iban-mehr-e-eghtesad	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک مهر اقتصاد	\N		تبدیل شماره حساب به شبا بانک مهر اقتصاد	\N	\N	تبدیل شماره حساب به شبا بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر اقتصاد	\N	t	\N	f
224	تبدیل شماره حساب به شبا - بانک مهر ایران	account-iban-mehr-e-iranian	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک مهر ایران	\N		تبدیل شماره حساب به شبا بانک مهر ایران	\N	\N	تبدیل شماره حساب به شبا بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر ایران	\N	t	\N	f
225	تبدیل شماره حساب به شبا - بانک ملل	account-iban-melal	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک ملل	\N		تبدیل شماره حساب به شبا بانک ملل	\N	\N	تبدیل شماره حساب به شبا بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملل	\N	t	\N	f
226	تبدیل شماره حساب به شبا - بانک پارسیان	account-iban-parsian	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک پارسیان	\N		تبدیل شماره حساب به شبا بانک پارسیان	\N	\N	تبدیل شماره حساب به شبا بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پارسیان	\N	t	\N	f
229	تبدیل شماره حساب به شبا - بانک صادرات	account-iban-saderat	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	1	0	0	تبدیل شماره حساب به شبا بانک صادرات	\N		تبدیل شماره حساب به شبا بانک صادرات	\N	\N	تبدیل شماره حساب به شبا بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:35:55	f	350	IRT	2500	\N	بانک صادرات	\N	t	\N	f
228	تبدیل شماره حساب به شبا - بانک رفاه	account-iban-refah	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک رفاه	\N		تبدیل شماره حساب به شبا بانک رفاه	\N	\N	تبدیل شماره حساب به شبا بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رفاه	\N	t	\N	f
227	تبدیل شماره حساب به شبا - پست بانک	account-iban-post-bank	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	1	0	0	تبدیل شماره حساب به شبا پست بانک	\N		تبدیل شماره حساب به شبا پست بانک	\N	\N	تبدیل شماره حساب به شبا پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:28:30	f	350	IRT	2500	\N	پست بانک	\N	t	\N	f
217	تبدیل شماره حساب به شبا - بانک دی	account-iban-dey	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک دی	\N		تبدیل شماره حساب به شبا بانک دی	\N	\N	تبدیل شماره حساب به شبا بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک دی	\N	t	\N	f
241	تبدیل شماره شبا به شماره حساب - بانک پاسارگاد	iban-account-pasargad	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	1	0	0	تبدیل شماره شبا به شماره حساب بانک پاسارگاد	\N		تبدیل شماره شبا به شماره حساب بانک پاسارگاد	\N	\N	تبدیل شماره شبا به شماره حساب بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:28:25	f	350	IRT	2500	\N	بانک پاسارگاد	\N	t	\N	f
233	تبدیل شماره حساب به شبا - بانک تجارت	account-iban-tejarat	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک تجارت	\N		تبدیل شماره حساب به شبا بانک تجارت	\N	\N	تبدیل شماره حساب به شبا بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک تجارت	\N	t	\N	f
234	تبدیل شماره حساب به شبا - بانک توسعه تعاون	account-iban-tosee-taavon	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک توسعه تعاون	\N		تبدیل شماره حساب به شبا بانک توسعه تعاون	\N	\N	تبدیل شماره حساب به شبا بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک توسعه تعاون	\N	t	\N	f
235	تبدیل شماره شبا به شماره حساب - بانک دی	iban-account-dey	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک دی	\N		تبدیل شماره شبا به شماره حساب بانک دی	\N	\N	تبدیل شماره شبا به شماره حساب بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک دی	\N	t	\N	f
236	تبدیل شماره شبا به شماره حساب - بانک کارآفرین	iban-account-karafarin	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک کارآفرین	\N		تبدیل شماره شبا به شماره حساب بانک کارآفرین	\N	\N	تبدیل شماره شبا به شماره حساب بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کارآفرین	\N	t	\N	f
237	تبدیل شماره شبا به شماره حساب - بانک کشاورزی	iban-account-keshavarzi	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک کشاورزی	\N		تبدیل شماره شبا به شماره حساب بانک کشاورزی	\N	\N	تبدیل شماره شبا به شماره حساب بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کشاورزی	\N	t	\N	f
238	تبدیل شماره شبا به شماره حساب - بانک خاورمیانه	iban-account-khavarmianeh	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک خاورمیانه	\N		تبدیل شماره شبا به شماره حساب بانک خاورمیانه	\N	\N	تبدیل شماره شبا به شماره حساب بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک خاورمیانه	\N	t	\N	f
239	تبدیل شماره شبا به شماره حساب - بانک مهر اقتصاد	iban-account-mehr-e-eghtesad	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک مهر اقتصاد	\N		تبدیل شماره شبا به شماره حساب بانک مهر اقتصاد	\N	\N	تبدیل شماره شبا به شماره حساب بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر اقتصاد	\N	t	\N	f
240	تبدیل شماره شبا به شماره حساب - بانک ملی	iban-account-melli	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک ملی	\N		تبدیل شماره شبا به شماره حساب بانک ملی	\N	\N	تبدیل شماره شبا به شماره حساب بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملی	\N	t	\N	f
242	تبدیل شماره شبا به شماره حساب - بانک رفاه	iban-account-refah	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک رفاه	\N		تبدیل شماره شبا به شماره حساب بانک رفاه	\N	\N	تبدیل شماره شبا به شماره حساب بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رفاه	\N	t	\N	f
243	تبدیل شماره شبا به شماره حساب - بانک شهر	iban-account-shahr	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک شهر	\N		تبدیل شماره شبا به شماره حساب بانک شهر	\N	\N	تبدیل شماره شبا به شماره حساب بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک شهر	\N	t	\N	f
232	تبدیل شماره حساب به شبا - بانک سینا	account-iban-sina	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	1	0	0	تبدیل شماره حساب به شبا بانک سینا	\N		تبدیل شماره حساب به شبا بانک سینا	\N	\N	تبدیل شماره حساب به شبا بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:28:24	f	350	IRT	2500	\N	بانک سینا	\N	t	\N	f
231	تبدیل شماره حساب به شبا - بانک شهر	account-iban-shahr	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک شهر	\N		تبدیل شماره حساب به شبا بانک شهر	\N	\N	تبدیل شماره حساب به شبا بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک شهر	\N	t	\N	f
247	استعلام و بررسی شماره شبا - بانک گردشگری	iban-check-gardeshgari	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک گردشگری	\N		استعلام و بررسی شماره شبا بانک گردشگری	\N	\N	استعلام و بررسی شماره شبا بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک گردشگری	\N	t	\N	f
248	استعلام و بررسی شماره شبا - بانک ایران زمین	iban-check-iranzamin	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک ایران زمین	\N		استعلام و بررسی شماره شبا بانک ایران زمین	\N	\N	استعلام و بررسی شماره شبا بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ایران زمین	\N	t	\N	f
249	استعلام و بررسی شماره شبا - بانک کارآفرین	iban-check-karafarin	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک کارآفرین	\N		استعلام و بررسی شماره شبا بانک کارآفرین	\N	\N	استعلام و بررسی شماره شبا بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کارآفرین	\N	t	\N	f
250	استعلام و بررسی شماره شبا - بانک کشاورزی	iban-check-keshavarzi	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک کشاورزی	\N		استعلام و بررسی شماره شبا بانک کشاورزی	\N	\N	استعلام و بررسی شماره شبا بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک کشاورزی	\N	t	\N	f
251	استعلام و بررسی شماره شبا - بانک خاورمیانه	iban-check-khavarmianeh	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک خاورمیانه	\N		استعلام و بررسی شماره شبا بانک خاورمیانه	\N	\N	استعلام و بررسی شماره شبا بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک خاورمیانه	\N	t	\N	f
252	استعلام و بررسی شماره شبا - بانک مسکن	iban-check-maskan	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک مسکن	\N		استعلام و بررسی شماره شبا بانک مسکن	\N	\N	استعلام و بررسی شماره شبا بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مسکن	\N	t	\N	f
254	استعلام و بررسی شماره شبا - بانک مهر ایران	iban-check-mehr-e-iranian	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک مهر ایران	\N		استعلام و بررسی شماره شبا بانک مهر ایران	\N	\N	استعلام و بررسی شماره شبا بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک مهر ایران	\N	t	\N	f
255	استعلام و بررسی شماره شبا - بانک ملل	iban-check-melal	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک ملل	\N		استعلام و بررسی شماره شبا بانک ملل	\N	\N	استعلام و بررسی شماره شبا بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملل	\N	t	\N	f
256	استعلام و بررسی شماره شبا - بانک ملت	iban-check-mellat	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک ملت	\N		استعلام و بررسی شماره شبا بانک ملت	\N	\N	استعلام و بررسی شماره شبا بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملت	\N	t	\N	f
257	استعلام و بررسی شماره شبا - بانک ملی	iban-check-melli	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک ملی	\N		استعلام و بررسی شماره شبا بانک ملی	\N	\N	استعلام و بررسی شماره شبا بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ملی	\N	t	\N	f
258	استعلام و بررسی شماره شبا - بانک پارسیان	iban-check-parsian	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک پارسیان	\N		استعلام و بررسی شماره شبا بانک پارسیان	\N	\N	استعلام و بررسی شماره شبا بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پارسیان	\N	t	\N	f
253	استعلام و بررسی شماره شبا - بانک مهر اقتصاد	iban-check-mehr-e-eghtesad	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	1	0	0	استعلام و بررسی شماره شبا بانک مهر اقتصاد	\N		استعلام و بررسی شماره شبا بانک مهر اقتصاد	\N	\N	استعلام و بررسی شماره شبا بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:28:34	f	350	IRT	2500	\N	بانک مهر اقتصاد	\N	t	\N	f
245	استعلام و بررسی شماره شبا - بانک دی	iban-check-dey	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک دی	\N		استعلام و بررسی شماره شبا بانک دی	\N	\N	استعلام و بررسی شماره شبا بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک دی	\N	t	\N	f
260	استعلام و بررسی شماره شبا - پست بانک	iban-check-post-bank	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا پست بانک	\N		استعلام و بررسی شماره شبا پست بانک	\N	\N	استعلام و بررسی شماره شبا پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	پست بانک	\N	t	\N	f
261	استعلام و بررسی شماره شبا - بانک رفاه	iban-check-refah	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک رفاه	\N		استعلام و بررسی شماره شبا بانک رفاه	\N	\N	استعلام و بررسی شماره شبا بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رفاه	\N	t	\N	f
262	استعلام و بررسی شماره شبا - بانک رسالت	iban-check-resalat	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک رسالت	\N		استعلام و بررسی شماره شبا بانک رسالت	\N	\N	استعلام و بررسی شماره شبا بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک رسالت	\N	t	\N	f
264	استعلام و بررسی شماره شبا - بانک سامان	iban-check-saman	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک سامان	\N		استعلام و بررسی شماره شبا بانک سامان	\N	\N	استعلام و بررسی شماره شبا بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سامان	\N	t	\N	f
265	استعلام و بررسی شماره شبا - بانک سپه	iban-check-sepah	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک سپه	\N		استعلام و بررسی شماره شبا بانک سپه	\N	\N	استعلام و بررسی شماره شبا بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک سپه	\N	t	\N	f
267	استعلام و بررسی شماره شبا - بانک سینا	iban-check-sina	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک سینا	\N		استعلام و بررسی شماره شبا بانک سینا	\N	\N	استعلام و بررسی شماره شبا بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک سینا	\N	t	\N	f
269	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک دی	credit-score-rating-dey	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک دی	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک دی	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک دی	\N	t	\N	f
270	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک اقتصاد نوین	credit-score-rating-eghtesad-novin	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک اقتصاد نوین	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک اقتصاد نوین	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک اقتصاد نوین	\N	t	\N	f
271	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک کارآفرین	credit-score-rating-karafarin	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک کارآفرین	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک کارآفرین	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک کارآفرین	\N	t	\N	f
272	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک مهر اقتصاد	credit-score-rating-mehr-e-eghtesad	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر اقتصاد	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر اقتصاد	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک مهر اقتصاد	\N	t	\N	f
288	استعلام ضمانت وام با کدملی - بانک دی	loan-guarantee-inquiry-dey	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک دی	\N		استعلام ضمانت وام با کدملی بانک دی	\N	\N	استعلام ضمانت وام با کدملی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک دی	\N	t	\N	f
263	استعلام و بررسی شماره شبا - بانک صادرات	iban-check-saderat	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	1	0	0	استعلام و بررسی شماره شبا بانک صادرات	\N		استعلام و بررسی شماره شبا بانک صادرات	\N	\N	استعلام و بررسی شماره شبا بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:35:54	f	350	IRT	2500	\N	بانک صادرات	\N	t	\N	f
259	استعلام و بررسی شماره شبا - بانک پاسارگاد	iban-check-pasargad	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک پاسارگاد	\N		استعلام و بررسی شماره شبا بانک پاسارگاد	\N	\N	استعلام و بررسی شماره شبا بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک پاسارگاد	\N	t	\N	f
275	استعلام وام و تسهیلات با کدملی - بانک اقتصاد نوین	loan-inquiry-eghtesad-novin	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک اقتصاد نوین	\N		استعلام وام و تسهیلات با کدملی بانک اقتصاد نوین	\N	\N	استعلام وام و تسهیلات با کدملی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک اقتصاد نوین	\N	t	\N	f
276	استعلام وام و تسهیلات با کدملی - بانک گردشگری	loan-inquiry-gardeshgari	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک گردشگری	\N		استعلام وام و تسهیلات با کدملی بانک گردشگری	\N	\N	استعلام وام و تسهیلات با کدملی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک گردشگری	\N	t	\N	f
277	استعلام وام و تسهیلات با کدملی - بانک ایران زمین	loan-inquiry-iranzamin	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک ایران زمین	\N		استعلام وام و تسهیلات با کدملی بانک ایران زمین	\N	\N	استعلام وام و تسهیلات با کدملی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک ایران زمین	\N	t	\N	f
278	استعلام وام و تسهیلات با کدملی - بانک کارآفرین	loan-inquiry-karafarin	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک کارآفرین	\N		استعلام وام و تسهیلات با کدملی بانک کارآفرین	\N	\N	استعلام وام و تسهیلات با کدملی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک کارآفرین	\N	t	\N	f
279	استعلام وام و تسهیلات با کدملی - بانک خاورمیانه	loan-inquiry-khavarmianeh	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک خاورمیانه	\N		استعلام وام و تسهیلات با کدملی بانک خاورمیانه	\N	\N	استعلام وام و تسهیلات با کدملی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک خاورمیانه	\N	t	\N	f
280	استعلام وام و تسهیلات با کدملی - بانک مهر اقتصاد	loan-inquiry-mehr-e-eghtesad	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک مهر اقتصاد	\N		استعلام وام و تسهیلات با کدملی بانک مهر اقتصاد	\N	\N	استعلام وام و تسهیلات با کدملی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک مهر اقتصاد	\N	t	\N	f
281	استعلام وام و تسهیلات با کدملی - بانک ملل	loan-inquiry-melal	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک ملل	\N		استعلام وام و تسهیلات با کدملی بانک ملل	\N	\N	استعلام وام و تسهیلات با کدملی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک ملل	\N	t	\N	f
282	استعلام وام و تسهیلات با کدملی - بانک ملی	loan-inquiry-melli	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک ملی	\N		استعلام وام و تسهیلات با کدملی بانک ملی	\N	\N	استعلام وام و تسهیلات با کدملی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک ملی	\N	t	\N	f
283	استعلام وام و تسهیلات با کدملی - بانک پاسارگاد	loan-inquiry-pasargad	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک پاسارگاد	\N		استعلام وام و تسهیلات با کدملی بانک پاسارگاد	\N	\N	استعلام وام و تسهیلات با کدملی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک پاسارگاد	\N	t	\N	f
284	استعلام وام و تسهیلات با کدملی - پست بانک	loan-inquiry-post-bank	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی پست بانک	\N		استعلام وام و تسهیلات با کدملی پست بانک	\N	\N	استعلام وام و تسهیلات با کدملی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	پست بانک	\N	t	\N	f
285	استعلام وام و تسهیلات با کدملی - بانک صادرات	loan-inquiry-saderat	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک صادرات	\N		استعلام وام و تسهیلات با کدملی بانک صادرات	\N	\N	استعلام وام و تسهیلات با کدملی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک صادرات	\N	t	\N	f
286	استعلام وام و تسهیلات با کدملی - بانک سامان	loan-inquiry-saman	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک سامان	\N		استعلام وام و تسهیلات با کدملی بانک سامان	\N	\N	استعلام وام و تسهیلات با کدملی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک سامان	\N	t	\N	f
287	استعلام وام و تسهیلات با کدملی - بانک شهر	loan-inquiry-shahr	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک شهر	\N		استعلام وام و تسهیلات با کدملی بانک شهر	\N	\N	استعلام وام و تسهیلات با کدملی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک شهر	\N	t	\N	f
274	استعلام وام و تسهیلات با کدملی - بانک دی	loan-inquiry-dey	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک دی	\N		استعلام وام و تسهیلات با کدملی بانک دی	\N	\N	استعلام وام و تسهیلات با کدملی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک دی	\N	t	\N	f
291	استعلام ضمانت وام با کدملی - بانک ایران زمین	loan-guarantee-inquiry-iranzamin	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک ایران زمین	\N		استعلام ضمانت وام با کدملی بانک ایران زمین	\N	\N	استعلام ضمانت وام با کدملی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک ایران زمین	\N	t	\N	f
292	استعلام ضمانت وام با کدملی - بانک کارآفرین	loan-guarantee-inquiry-karafarin	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک کارآفرین	\N		استعلام ضمانت وام با کدملی بانک کارآفرین	\N	\N	استعلام ضمانت وام با کدملی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک کارآفرین	\N	t	\N	f
293	استعلام ضمانت وام با کدملی - بانک کشاورزی	loan-guarantee-inquiry-keshavarzi	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک کشاورزی	\N		استعلام ضمانت وام با کدملی بانک کشاورزی	\N	\N	استعلام ضمانت وام با کدملی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک کشاورزی	\N	t	\N	f
294	استعلام ضمانت وام با کدملی - بانک خاورمیانه	loan-guarantee-inquiry-khavarmianeh	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک خاورمیانه	\N		استعلام ضمانت وام با کدملی بانک خاورمیانه	\N	\N	استعلام ضمانت وام با کدملی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک خاورمیانه	\N	t	\N	f
296	استعلام ضمانت وام با کدملی - بانک مهر اقتصاد	loan-guarantee-inquiry-mehr-e-eghtesad	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک مهر اقتصاد	\N		استعلام ضمانت وام با کدملی بانک مهر اقتصاد	\N	\N	استعلام ضمانت وام با کدملی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک مهر اقتصاد	\N	t	\N	f
297	استعلام ضمانت وام با کدملی - بانک مهر ایران	loan-guarantee-inquiry-mehr-e-iranian	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک مهر ایران	\N		استعلام ضمانت وام با کدملی بانک مهر ایران	\N	\N	استعلام ضمانت وام با کدملی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک مهر ایران	\N	t	\N	f
298	استعلام ضمانت وام با کدملی - بانک ملل	loan-guarantee-inquiry-melal	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک ملل	\N		استعلام ضمانت وام با کدملی بانک ملل	\N	\N	استعلام ضمانت وام با کدملی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک ملل	\N	t	\N	f
299	استعلام ضمانت وام با کدملی - بانک ملت	loan-guarantee-inquiry-mellat	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک ملت	\N		استعلام ضمانت وام با کدملی بانک ملت	\N	\N	استعلام ضمانت وام با کدملی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک ملت	\N	t	\N	f
300	استعلام ضمانت وام با کدملی - بانک ملی	loan-guarantee-inquiry-melli	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک ملی	\N		استعلام ضمانت وام با کدملی بانک ملی	\N	\N	استعلام ضمانت وام با کدملی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک ملی	\N	t	\N	f
301	استعلام ضمانت وام با کدملی - بانک پارسیان	loan-guarantee-inquiry-parsian	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک پارسیان	\N		استعلام ضمانت وام با کدملی بانک پارسیان	\N	\N	استعلام ضمانت وام با کدملی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک پارسیان	\N	t	\N	f
302	استعلام ضمانت وام با کدملی - بانک پاسارگاد	loan-guarantee-inquiry-pasargad	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک پاسارگاد	\N		استعلام ضمانت وام با کدملی بانک پاسارگاد	\N	\N	استعلام ضمانت وام با کدملی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک پاسارگاد	\N	t	\N	f
303	استعلام ضمانت وام با کدملی - پست بانک	loan-guarantee-inquiry-post-bank	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی پست بانک	\N		استعلام ضمانت وام با کدملی پست بانک	\N	\N	استعلام ضمانت وام با کدملی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	پست بانک	\N	t	\N	f
304	استعلام ضمانت وام با کدملی - بانک رفاه	loan-guarantee-inquiry-refah	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک رفاه	\N		استعلام ضمانت وام با کدملی بانک رفاه	\N	\N	استعلام ضمانت وام با کدملی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک رفاه	\N	t	\N	f
295	استعلام ضمانت وام با کدملی - بانک مسکن	loan-guarantee-inquiry-maskan	<p>test</p>	1	\N	\N	active	f	1	16	1	0	0	استعلام ضمانت وام با کدملی بانک مسکن	\N		استعلام ضمانت وام با کدملی بانک مسکن	\N	\N	استعلام ضمانت وام با کدملی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:36:04	f	5000	IRR	12500	\N	بانک مسکن	\N	t	\N	f
290	استعلام ضمانت وام با کدملی - بانک گردشگری	loan-guarantee-inquiry-gardeshgari	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک گردشگری	\N		استعلام ضمانت وام با کدملی بانک گردشگری	\N	\N	استعلام ضمانت وام با کدملی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک گردشگری	\N	t	\N	f
306	استعلام ضمانت وام با کدملی - بانک صادرات	loan-guarantee-inquiry-saderat	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک صادرات	\N		استعلام ضمانت وام با کدملی بانک صادرات	\N	\N	استعلام ضمانت وام با کدملی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک صادرات	\N	t	\N	f
308	استعلام ضمانت وام با کدملی - بانک سپه	loan-guarantee-inquiry-sepah	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک سپه	\N		استعلام ضمانت وام با کدملی بانک سپه	\N	\N	استعلام ضمانت وام با کدملی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک سپه	\N	t	\N	f
309	استعلام ضمانت وام با کدملی - بانک شهر	loan-guarantee-inquiry-shahr	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک شهر	\N		استعلام ضمانت وام با کدملی بانک شهر	\N	\N	استعلام ضمانت وام با کدملی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک شهر	\N	t	\N	f
310	استعلام ضمانت وام با کدملی - بانک سینا	loan-guarantee-inquiry-sina	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک سینا	\N		استعلام ضمانت وام با کدملی بانک سینا	\N	\N	استعلام ضمانت وام با کدملی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک سینا	\N	t	\N	f
311	استعلام ضمانت وام با کدملی - بانک تجارت	loan-guarantee-inquiry-tejarat	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک تجارت	\N		استعلام ضمانت وام با کدملی بانک تجارت	\N	\N	استعلام ضمانت وام با کدملی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک تجارت	\N	t	\N	f
317	استعلام اعتبار و محکومیت مالی - بانک کارآفرین	financial-judgment-inquiry-karafarin	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی بانک کارآفرین	\N		استعلام اعتبار و محکومیت مالی بانک کارآفرین	\N	\N	استعلام اعتبار و محکومیت مالی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:36:06	f	5000	IRR	10000	\N	بانک کارآفرین	\N	t	\N	f
314	استعلام اعتبار و محکومیت مالی - بانک اقتصاد نوین	financial-judgment-inquiry-eghtesad-novin	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک اقتصاد نوین	\N		استعلام اعتبار و محکومیت مالی بانک اقتصاد نوین	\N	\N	استعلام اعتبار و محکومیت مالی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک اقتصاد نوین	\N	t	\N	f
315	استعلام اعتبار و محکومیت مالی - بانک گردشگری	financial-judgment-inquiry-gardeshgari	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک گردشگری	\N		استعلام اعتبار و محکومیت مالی بانک گردشگری	\N	\N	استعلام اعتبار و محکومیت مالی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک گردشگری	\N	t	\N	f
316	استعلام اعتبار و محکومیت مالی - بانک ایران زمین	financial-judgment-inquiry-iranzamin	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک ایران زمین	\N		استعلام اعتبار و محکومیت مالی بانک ایران زمین	\N	\N	استعلام اعتبار و محکومیت مالی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ایران زمین	\N	t	\N	f
318	استعلام اعتبار و محکومیت مالی - بانک کشاورزی	financial-judgment-inquiry-keshavarzi	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی بانک کشاورزی	\N		استعلام اعتبار و محکومیت مالی بانک کشاورزی	\N	\N	استعلام اعتبار و محکومیت مالی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:36:07	f	5000	IRR	10000	\N	بانک کشاورزی	\N	t	\N	f
312	استعلام ضمانت وام با کدملی - بانک توسعه تعاون	loan-guarantee-inquiry-tosee-taavon	<p>test</p>	1	\N	\N	active	f	1	16	2	0	0	استعلام ضمانت وام با کدملی بانک توسعه تعاون	\N		استعلام ضمانت وام با کدملی بانک توسعه تعاون	\N	\N	استعلام ضمانت وام با کدملی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:39:59	f	5000	IRR	12500	\N	بانک توسعه تعاون	\N	t	\N	f
319	استعلام اعتبار و محکومیت مالی - بانک خاورمیانه	financial-judgment-inquiry-khavarmianeh	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک خاورمیانه	\N		استعلام اعتبار و محکومیت مالی بانک خاورمیانه	\N	\N	استعلام اعتبار و محکومیت مالی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک خاورمیانه	\N	t	\N	f
307	استعلام ضمانت وام با کدملی - بانک سامان	loan-guarantee-inquiry-saman	<p>test</p>	1	\N	\N	active	f	1	16	1	0	0	استعلام ضمانت وام با کدملی بانک سامان	\N		استعلام ضمانت وام با کدملی بانک سامان	\N	\N	استعلام ضمانت وام با کدملی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:36:01	f	5000	IRR	12500	\N	بانک سامان	\N	t	\N	f
305	استعلام ضمانت وام با کدملی - بانک رسالت	loan-guarantee-inquiry-resalat	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک رسالت	\N		استعلام ضمانت وام با کدملی بانک رسالت	\N	\N	استعلام ضمانت وام با کدملی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک رسالت	\N	t	\N	f
321	استعلام اعتبار و محکومیت مالی - بانک مهر اقتصاد	financial-judgment-inquiry-mehr-e-eghtesad	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک مهر اقتصاد	\N		استعلام اعتبار و محکومیت مالی بانک مهر اقتصاد	\N	\N	استعلام اعتبار و محکومیت مالی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مهر اقتصاد	\N	t	\N	f
322	استعلام اعتبار و محکومیت مالی - بانک مهر ایران	financial-judgment-inquiry-mehr-e-iranian	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک مهر ایران	\N		استعلام اعتبار و محکومیت مالی بانک مهر ایران	\N	\N	استعلام اعتبار و محکومیت مالی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مهر ایران	\N	t	\N	f
323	استعلام اعتبار و محکومیت مالی - بانک ملل	financial-judgment-inquiry-melal	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک ملل	\N		استعلام اعتبار و محکومیت مالی بانک ملل	\N	\N	استعلام اعتبار و محکومیت مالی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ملل	\N	t	\N	f
324	استعلام اعتبار و محکومیت مالی - بانک ملت	financial-judgment-inquiry-mellat	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک ملت	\N		استعلام اعتبار و محکومیت مالی بانک ملت	\N	\N	استعلام اعتبار و محکومیت مالی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ملت	\N	t	\N	f
325	استعلام اعتبار و محکومیت مالی - بانک ملی	financial-judgment-inquiry-melli	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک ملی	\N		استعلام اعتبار و محکومیت مالی بانک ملی	\N	\N	استعلام اعتبار و محکومیت مالی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ملی	\N	t	\N	f
328	استعلام اعتبار و محکومیت مالی - پست بانک	financial-judgment-inquiry-post-bank	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی پست بانک	\N		استعلام اعتبار و محکومیت مالی پست بانک	\N	\N	استعلام اعتبار و محکومیت مالی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:36:05	f	5000	IRR	10000	\N	پست بانک	\N	t	\N	f
327	استعلام اعتبار و محکومیت مالی - بانک پاسارگاد	financial-judgment-inquiry-pasargad	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک پاسارگاد	\N		استعلام اعتبار و محکومیت مالی بانک پاسارگاد	\N	\N	استعلام اعتبار و محکومیت مالی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک پاسارگاد	\N	t	\N	f
330	استعلام اعتبار و محکومیت مالی - بانک رسالت	financial-judgment-inquiry-resalat	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک رسالت	\N		استعلام اعتبار و محکومیت مالی بانک رسالت	\N	\N	استعلام اعتبار و محکومیت مالی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک رسالت	\N	t	\N	f
331	استعلام اعتبار و محکومیت مالی - بانک صادرات	financial-judgment-inquiry-saderat	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک صادرات	\N		استعلام اعتبار و محکومیت مالی بانک صادرات	\N	\N	استعلام اعتبار و محکومیت مالی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک صادرات	\N	t	\N	f
332	استعلام اعتبار و محکومیت مالی - بانک سامان	financial-judgment-inquiry-saman	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک سامان	\N		استعلام اعتبار و محکومیت مالی بانک سامان	\N	\N	استعلام اعتبار و محکومیت مالی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سامان	\N	t	\N	f
333	استعلام اعتبار و محکومیت مالی - بانک سپه	financial-judgment-inquiry-sepah	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک سپه	\N		استعلام اعتبار و محکومیت مالی بانک سپه	\N	\N	استعلام اعتبار و محکومیت مالی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سپه	\N	t	\N	f
334	استعلام اعتبار و محکومیت مالی - بانک شهر	financial-judgment-inquiry-shahr	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک شهر	\N		استعلام اعتبار و محکومیت مالی بانک شهر	\N	\N	استعلام اعتبار و محکومیت مالی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک شهر	\N	t	\N	f
326	استعلام اعتبار و محکومیت مالی - بانک پارسیان	financial-judgment-inquiry-parsian	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی بانک پارسیان	\N		استعلام اعتبار و محکومیت مالی بانک پارسیان	\N	\N	استعلام اعتبار و محکومیت مالی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:28:46	f	5000	IRR	10000	\N	بانک پارسیان	\N	t	\N	f
320	استعلام اعتبار و محکومیت مالی - بانک مسکن	financial-judgment-inquiry-maskan	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک مسکن	\N		استعلام اعتبار و محکومیت مالی بانک مسکن	\N	\N	استعلام اعتبار و محکومیت مالی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مسکن	\N	t	\N	f
336	استعلام اعتبار و محکومیت مالی - بانک تجارت	financial-judgment-inquiry-tejarat	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک تجارت	\N		استعلام اعتبار و محکومیت مالی بانک تجارت	\N	\N	استعلام اعتبار و محکومیت مالی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک تجارت	\N	t	\N	f
337	استعلام اعتبار و محکومیت مالی - بانک توسعه تعاون	financial-judgment-inquiry-tosee-taavon	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک توسعه تعاون	\N		استعلام اعتبار و محکومیت مالی بانک توسعه تعاون	\N	\N	استعلام اعتبار و محکومیت مالی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک توسعه تعاون	\N	t	\N	f
338	استعلام و دریافت شماره شهاب - بانک دی	shahab-number-dey	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک دی	\N		استعلام و دریافت شماره شهاب بانک دی	\N	\N	استعلام و دریافت شماره شهاب بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک دی	\N	t	\N	f
339	استعلام و دریافت شماره شهاب - بانک اقتصاد نوین	shahab-number-eghtesad-novin	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک اقتصاد نوین	\N		استعلام و دریافت شماره شهاب بانک اقتصاد نوین	\N	\N	استعلام و دریافت شماره شهاب بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک اقتصاد نوین	\N	t	\N	f
340	استعلام و دریافت شماره شهاب - بانک گردشگری	shahab-number-gardeshgari	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک گردشگری	\N		استعلام و دریافت شماره شهاب بانک گردشگری	\N	\N	استعلام و دریافت شماره شهاب بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک گردشگری	\N	t	\N	f
341	استعلام و دریافت شماره شهاب - بانک ایران زمین	shahab-number-iranzamin	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک ایران زمین	\N		استعلام و دریافت شماره شهاب بانک ایران زمین	\N	\N	استعلام و دریافت شماره شهاب بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک ایران زمین	\N	t	\N	f
342	استعلام و دریافت شماره شهاب - بانک کارآفرین	shahab-number-karafarin	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک کارآفرین	\N		استعلام و دریافت شماره شهاب بانک کارآفرین	\N	\N	استعلام و دریافت شماره شهاب بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک کارآفرین	\N	t	\N	f
343	استعلام و دریافت شماره شهاب - بانک کشاورزی	shahab-number-keshavarzi	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک کشاورزی	\N		استعلام و دریافت شماره شهاب بانک کشاورزی	\N	\N	استعلام و دریافت شماره شهاب بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک کشاورزی	\N	t	\N	f
344	استعلام و دریافت شماره شهاب - بانک خاورمیانه	shahab-number-khavarmianeh	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک خاورمیانه	\N		استعلام و دریافت شماره شهاب بانک خاورمیانه	\N	\N	استعلام و دریافت شماره شهاب بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک خاورمیانه	\N	t	\N	f
345	استعلام و دریافت شماره شهاب - بانک مسکن	shahab-number-maskan	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک مسکن	\N		استعلام و دریافت شماره شهاب بانک مسکن	\N	\N	استعلام و دریافت شماره شهاب بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک مسکن	\N	t	\N	f
346	استعلام و دریافت شماره شهاب - بانک مهر اقتصاد	shahab-number-mehr-e-eghtesad	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک مهر اقتصاد	\N		استعلام و دریافت شماره شهاب بانک مهر اقتصاد	\N	\N	استعلام و دریافت شماره شهاب بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک مهر اقتصاد	\N	t	\N	f
348	استعلام و دریافت شماره شهاب - بانک ملل	shahab-number-melal	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک ملل	\N		استعلام و دریافت شماره شهاب بانک ملل	\N	\N	استعلام و دریافت شماره شهاب بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک ملل	\N	t	\N	f
349	استعلام و دریافت شماره شهاب - بانک ملت	shahab-number-mellat	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک ملت	\N		استعلام و دریافت شماره شهاب بانک ملت	\N	\N	استعلام و دریافت شماره شهاب بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک ملت	\N	t	\N	f
335	استعلام اعتبار و محکومیت مالی - بانک سینا	financial-judgment-inquiry-sina	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	0	0	0	استعلام اعتبار و محکومیت مالی بانک سینا	\N		استعلام اعتبار و محکومیت مالی بانک سینا	\N	\N	استعلام اعتبار و محکومیت مالی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سینا	\N	t	\N	f
352	استعلام و دریافت شماره شهاب - بانک پاسارگاد	shahab-number-pasargad	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک پاسارگاد	\N		استعلام و دریافت شماره شهاب بانک پاسارگاد	\N	\N	استعلام و دریافت شماره شهاب بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک پاسارگاد	\N	t	\N	f
353	استعلام و دریافت شماره شهاب - پست بانک	shahab-number-post-bank	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب پست بانک	\N		استعلام و دریافت شماره شهاب پست بانک	\N	\N	استعلام و دریافت شماره شهاب پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	پست بانک	\N	t	\N	f
354	استعلام و دریافت شماره شهاب - بانک رفاه	shahab-number-refah	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک رفاه	\N		استعلام و دریافت شماره شهاب بانک رفاه	\N	\N	استعلام و دریافت شماره شهاب بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک رفاه	\N	t	\N	f
355	استعلام و دریافت شماره شهاب - بانک رسالت	shahab-number-resalat	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک رسالت	\N		استعلام و دریافت شماره شهاب بانک رسالت	\N	\N	استعلام و دریافت شماره شهاب بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک رسالت	\N	t	\N	f
356	استعلام و دریافت شماره شهاب - بانک صادرات	shahab-number-saderat	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک صادرات	\N		استعلام و دریافت شماره شهاب بانک صادرات	\N	\N	استعلام و دریافت شماره شهاب بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک صادرات	\N	t	\N	f
357	استعلام و دریافت شماره شهاب - بانک سامان	shahab-number-saman	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک سامان	\N		استعلام و دریافت شماره شهاب بانک سامان	\N	\N	استعلام و دریافت شماره شهاب بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک سامان	\N	t	\N	f
358	استعلام و دریافت شماره شهاب - بانک سپه	shahab-number-sepah	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک سپه	\N		استعلام و دریافت شماره شهاب بانک سپه	\N	\N	استعلام و دریافت شماره شهاب بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک سپه	\N	t	\N	f
359	استعلام و دریافت شماره شهاب - بانک شهر	shahab-number-shahr	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک شهر	\N		استعلام و دریافت شماره شهاب بانک شهر	\N	\N	استعلام و دریافت شماره شهاب بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک شهر	\N	t	\N	f
360	استعلام و دریافت شماره شهاب - بانک سینا	shahab-number-sina	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک سینا	\N		استعلام و دریافت شماره شهاب بانک سینا	\N	\N	استعلام و دریافت شماره شهاب بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک سینا	\N	t	\N	f
361	استعلام و دریافت شماره شهاب - بانک تجارت	shahab-number-tejarat	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک تجارت	\N		استعلام و دریافت شماره شهاب بانک تجارت	\N	\N	استعلام و دریافت شماره شهاب بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک تجارت	\N	t	\N	f
362	استعلام و دریافت شماره شهاب - بانک توسعه تعاون	shahab-number-tosee-taavon	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک توسعه تعاون	\N		استعلام و دریافت شماره شهاب بانک توسعه تعاون	\N	\N	استعلام و دریافت شماره شهاب بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک توسعه تعاون	\N	t	\N	f
363	استعلام کد مکنا با کدملی - بانک دی	inquiry-makna-code-dey	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک دی	\N		استعلام کد مکنا با کدملی بانک دی	\N	\N	استعلام کد مکنا با کدملی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک دی	\N	t	\N	f
364	استعلام کد مکنا با کدملی - بانک اقتصاد نوین	inquiry-makna-code-eghtesad-novin	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک اقتصاد نوین	\N		استعلام کد مکنا با کدملی بانک اقتصاد نوین	\N	\N	استعلام کد مکنا با کدملی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک اقتصاد نوین	\N	t	\N	f
365	استعلام کد مکنا با کدملی - بانک گردشگری	inquiry-makna-code-gardeshgari	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک گردشگری	\N		استعلام کد مکنا با کدملی بانک گردشگری	\N	\N	استعلام کد مکنا با کدملی بانک گردشگری	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک گردشگری	\N	t	\N	f
351	استعلام و دریافت شماره شهاب - بانک پارسیان	shahab-number-parsian	<p>test</p>	1	\N	\N	active	f	1	7	1	0	0	استعلام و دریافت شماره شهاب بانک پارسیان	\N		استعلام و دریافت شماره شهاب بانک پارسیان	\N	\N	استعلام و دریافت شماره شهاب بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:28:27	f	2500	IRT	5000	\N	بانک پارسیان	\N	t	\N	f
368	استعلام کد مکنا با کدملی - بانک کشاورزی	inquiry-makna-code-keshavarzi	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک کشاورزی	\N		استعلام کد مکنا با کدملی بانک کشاورزی	\N	\N	استعلام کد مکنا با کدملی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک کشاورزی	\N	t	\N	f
369	استعلام کد مکنا با کدملی - بانک خاورمیانه	inquiry-makna-code-khavarmianeh	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک خاورمیانه	\N		استعلام کد مکنا با کدملی بانک خاورمیانه	\N	\N	استعلام کد مکنا با کدملی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک خاورمیانه	\N	t	\N	f
370	استعلام کد مکنا با کدملی - بانک مسکن	inquiry-makna-code-maskan	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک مسکن	\N		استعلام کد مکنا با کدملی بانک مسکن	\N	\N	استعلام کد مکنا با کدملی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مسکن	\N	t	\N	f
371	استعلام کد مکنا با کدملی - بانک مهر اقتصاد	inquiry-makna-code-mehr-e-eghtesad	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک مهر اقتصاد	\N		استعلام کد مکنا با کدملی بانک مهر اقتصاد	\N	\N	استعلام کد مکنا با کدملی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مهر اقتصاد	\N	t	\N	f
372	استعلام کد مکنا با کدملی - بانک مهر ایران	inquiry-makna-code-mehr-e-iranian	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک مهر ایران	\N		استعلام کد مکنا با کدملی بانک مهر ایران	\N	\N	استعلام کد مکنا با کدملی بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مهر ایران	\N	t	\N	f
374	استعلام کد مکنا با کدملی - بانک ملت	inquiry-makna-code-mellat	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک ملت	\N		استعلام کد مکنا با کدملی بانک ملت	\N	\N	استعلام کد مکنا با کدملی بانک ملت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ملت	\N	t	\N	f
375	استعلام کد مکنا با کدملی - بانک ملی	inquiry-makna-code-melli	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک ملی	\N		استعلام کد مکنا با کدملی بانک ملی	\N	\N	استعلام کد مکنا با کدملی بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ملی	\N	t	\N	f
376	استعلام کد مکنا با کدملی - بانک پارسیان	inquiry-makna-code-parsian	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک پارسیان	\N		استعلام کد مکنا با کدملی بانک پارسیان	\N	\N	استعلام کد مکنا با کدملی بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک پارسیان	\N	t	\N	f
377	استعلام کد مکنا با کدملی - بانک پاسارگاد	inquiry-makna-code-pasargad	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک پاسارگاد	\N		استعلام کد مکنا با کدملی بانک پاسارگاد	\N	\N	استعلام کد مکنا با کدملی بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک پاسارگاد	\N	t	\N	f
378	استعلام کد مکنا با کدملی - پست بانک	inquiry-makna-code-post-bank	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی پست بانک	\N		استعلام کد مکنا با کدملی پست بانک	\N	\N	استعلام کد مکنا با کدملی پست بانک	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	پست بانک	\N	t	\N	f
367	استعلام کد مکنا با کدملی - بانک کارآفرین	inquiry-makna-code-karafarin	<p>test</p>	1	\N	\N	active	f	1	17	1	0	0	استعلام کد مکنا با کدملی بانک کارآفرین	\N		استعلام کد مکنا با کدملی بانک کارآفرین	\N	\N	استعلام کد مکنا با کدملی بانک کارآفرین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:35:59	f	5000	IRR	10000	\N	بانک کارآفرین	\N	t	\N	f
379	استعلام کد مکنا با کدملی - بانک رفاه	inquiry-makna-code-refah	<p>test</p>	1	\N	\N	active	f	1	17	1	0	0	استعلام کد مکنا با کدملی بانک رفاه	\N		استعلام کد مکنا با کدملی بانک رفاه	\N	\N	استعلام کد مکنا با کدملی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:35:57	f	5000	IRR	10000	\N	بانک رفاه	\N	t	\N	f
381	استعلام کد مکنا با کدملی - بانک صادرات	inquiry-makna-code-saderat	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک صادرات	\N		استعلام کد مکنا با کدملی بانک صادرات	\N	\N	استعلام کد مکنا با کدملی بانک صادرات	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک صادرات	\N	t	\N	f
380	استعلام کد مکنا با کدملی - بانک رسالت	inquiry-makna-code-resalat	<p>test</p>	1	\N	\N	active	f	1	17	1	0	0	استعلام کد مکنا با کدملی بانک رسالت	\N		استعلام کد مکنا با کدملی بانک رسالت	\N	\N	استعلام کد مکنا با کدملی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 19:59:09	f	5000	IRR	10000	\N	بانک رسالت	\N	t	\N	f
366	استعلام کد مکنا با کدملی - بانک ایران زمین	inquiry-makna-code-iranzamin	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک ایران زمین	\N		استعلام کد مکنا با کدملی بانک ایران زمین	\N	\N	استعلام کد مکنا با کدملی بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک ایران زمین	\N	t	\N	f
384	استعلام کد مکنا با کدملی - بانک شهر	inquiry-makna-code-shahr	<p>test</p>	1	\N	\N	active	f	1	17	2	0	0	استعلام کد مکنا با کدملی بانک شهر	\N		استعلام کد مکنا با کدملی بانک شهر	\N	\N	استعلام کد مکنا با کدملی بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:28:39	f	5000	IRR	10000	\N	بانک شهر	\N	t	\N	f
385	استعلام کد مکنا با کدملی - بانک سینا	inquiry-makna-code-sina	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک سینا	\N		استعلام کد مکنا با کدملی بانک سینا	\N	\N	استعلام کد مکنا با کدملی بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سینا	\N	t	\N	f
441	فیش بازنشستگان کشوری	teachers-retiree-payslip	<p>فیش بازنشستگان کشوری - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس فیش بازنشستگان کشوری در پیشخوانک	active	f	1	\N	0	0	0	فیش بازنشستگان کشوری	سرویس آنلاین فیش بازنشستگان کشوری در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 19:16:12	t	8000	IRR	10000	\N	\N	\N	t	\N	f
442	حکم تامین اجتماعی	hokmmostamari	<p>حکم تامین اجتماعی - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس حکم تامین اجتماعی در پیشخوانک	active	f	1	\N	1	0	0	حکم تامین اجتماعی	سرویس آنلاین حکم تامین اجتماعی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:28:04	t	8000	IRR	10000	\N	\N	\N	t	\N	f
444	اعتبار دفترچه بیمه	insurancecard	<p>اعتبار دفترچه بیمه - سرویس آنلاین پیشخوانک</p>	10	\N	سرویس اعتبار دفترچه بیمه در پیشخوانک	active	f	1	\N	2	0	0	اعتبار دفترچه بیمه	سرویس آنلاین اعتبار دفترچه بیمه در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:28:06	t	8000	IRR	10000	\N	\N	\N	t	\N	f
382	استعلام کد مکنا با کدملی - بانک سامان	inquiry-makna-code-saman	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک سامان	\N		استعلام کد مکنا با کدملی بانک سامان	\N	\N	استعلام کد مکنا با کدملی بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سامان	\N	t	\N	f
383	استعلام کد مکنا با کدملی - بانک سپه	inquiry-makna-code-sepah	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک سپه	\N		استعلام کد مکنا با کدملی بانک سپه	\N	\N	استعلام کد مکنا با کدملی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک سپه	\N	t	\N	f
386	استعلام کد مکنا با کدملی - بانک تجارت	inquiry-makna-code-tejarat	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک تجارت	\N		استعلام کد مکنا با کدملی بانک تجارت	\N	\N	استعلام کد مکنا با کدملی بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک تجارت	\N	t	\N	f
387	استعلام کد مکنا با کدملی - بانک توسعه تعاون	inquiry-makna-code-tosee-taavon	<p>test</p>	1	\N	\N	active	f	1	17	0	0	0	استعلام کد مکنا با کدملی بانک توسعه تعاون	\N		استعلام کد مکنا با کدملی بانک توسعه تعاون	\N	\N	استعلام کد مکنا با کدملی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک توسعه تعاون	\N	t	\N	f
443	حکم بازنشستگان کشوری	hokmbazneshasteghi	<p>حکم بازنشستگان کشوری - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس حکم بازنشستگان کشوری در پیشخوانک	active	f	1	\N	1	0	0	حکم بازنشستگان کشوری	سرویس آنلاین حکم بازنشستگان کشوری در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:28:16	t	8000	IRR	10000	\N	\N	\N	t	\N	f
449	معافیت تحصیلی	study-exemption	<p>معافیت تحصیلی - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس معافیت تحصیلی در پیشخوانک	active	f	1	\N	1	0	0	معافیت تحصیلی	سرویس آنلاین معافیت تحصیلی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:09	t	8000	IRR	10000	\N	\N	\N	t	\N	f
446	پیگیری کارت ملی	national-id-card	<p>پیگیری کارت ملی - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس پیگیری کارت ملی در پیشخوانک	active	f	1	\N	1	0	0	پیگیری کارت ملی	سرویس آنلاین پیگیری کارت ملی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:12	t	8000	IRR	10000	\N	\N	\N	t	\N	f
447	کالابرگ الکترونیکی	electronic-subsidy	<p>کالابرگ الکترونیکی - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس کالابرگ الکترونیکی در پیشخوانک	active	f	1	\N	1	0	0	کالابرگ الکترونیکی	سرویس آنلاین کالابرگ الکترونیکی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:17	t	8000	IRR	10000	\N	\N	\N	t	\N	f
445	اجاره نامه ملک	rentalcontractinquiry	<p>اجاره نامه ملک - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس اجاره نامه ملک در پیشخوانک	active	f	1	\N	1	0	0	اجاره نامه ملک	سرویس آنلاین اجاره نامه ملک در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:20	t	8000	IRR	10000	\N	\N	\N	t	\N	f
448	سیم‌کارت‌های به‌نام	my-simcart	<p>سیم‌کارت‌های به‌نام - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس سیم‌کارت‌های به‌نام در پیشخوانک	active	f	1	\N	4	0	0	سیم‌کارت‌های به‌نام	سرویس آنلاین سیم‌کارت‌های به‌نام در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:55	t	8000	IRR	10000	\N	\N	\N	t	\N	f
450	خلافی خودرو	khalafi	<p>خلافی خودرو - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس خلافی خودرو در پیشخوانک	active	f	1	\N	1	0	0	خلافی خودرو	سرویس آنلاین خلافی خودرو در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:47	t	8000	IRR	10000	\N	\N	\N	t	\N	f
451	خلافی موتورسیکلت	khalafimotor	<p>خلافی موتورسیکلت - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس خلافی موتورسیکلت در پیشخوانک	active	f	1	\N	1	0	0	خلافی موتورسیکلت	سرویس آنلاین خلافی موتورسیکلت در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:01	t	8000	IRR	10000	\N	\N	\N	t	\N	f
452	عوارض آزادراهی	freeway	<p>عوارض آزادراهی - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس عوارض آزادراهی در پیشخوانک	active	f	1	\N	1	0	0	عوارض آزادراهی	سرویس آنلاین عوارض آزادراهی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:49	t	8000	IRR	10000	\N	\N	\N	t	\N	f
453	عوارض سالیانه خودرو	avarezkhodro	<p>عوارض سالیانه خودرو - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس عوارض سالیانه خودرو در پیشخوانک	active	f	1	\N	1	0	0	عوارض سالیانه خودرو	سرویس آنلاین عوارض سالیانه خودرو در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:02	t	8000	IRR	10000	\N	\N	\N	t	\N	f
454	جریمه طرح ترافیک	shahrdarikhodro	<p>جریمه طرح ترافیک - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس جریمه طرح ترافیک در پیشخوانک	active	f	1	\N	1	0	0	جریمه طرح ترافیک	سرویس آنلاین جریمه طرح ترافیک در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:10	t	8000	IRR	10000	\N	\N	\N	t	\N	f
437	یارانه	yaraneh	<p>یارانه - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس یارانه در پیشخوانک	active	f	1	\N	1	0	0	یارانه	سرویس آنلاین یارانه در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:27:48	t	8000	IRR	10000	\N	\N	\N	t	\N	f
440	فیش تامین اجتماعی	fishmostamari	<p>فیش تامین اجتماعی - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس فیش تامین اجتماعی در پیشخوانک	active	f	1	\N	1	0	0	فیش تامین اجتماعی	سرویس آنلاین فیش تامین اجتماعی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:28:05	t	8000	IRR	10000	\N	\N	\N	t	\N	f
435	نسخه تامین اجتماعی	electronicprescribe	<p>نسخه تامین اجتماعی - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس نسخه تامین اجتماعی در پیشخوانک	active	f	1	\N	1	0	0	نسخه تامین اجتماعی	سرویس آنلاین نسخه تامین اجتماعی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:28:18	t	8000	IRR	10000	\N	\N	\N	t	\N	f
266	استعلام و بررسی شماره شبا - بانک شهر	iban-check-shahr	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	3	0	0	استعلام و بررسی شماره شبا بانک شهر	\N		استعلام و بررسی شماره شبا بانک شهر	\N	\N	استعلام و بررسی شماره شبا بانک شهر	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 20:41:48	f	350	IRT	2500	\N	بانک شهر	\N	t	\N	f
439	بیمه‌نامه‌	insurancepolicy	<p>بیمه‌نامه‌ - سرویس آنلاین پیشخوانک</p>	10	\N	سرویس بیمه‌نامه‌ در پیشخوانک	active	f	1	\N	6	0	0	بیمه‌نامه‌	سرویس آنلاین بیمه‌نامه‌ در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:43:02	t	8000	IRR	10000	\N	\N	\N	t	\N	f
438	دهک معیشتی	subsidy-ranking	<p>دهک معیشتی - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس دهک معیشتی در پیشخوانک	active	f	1	\N	1	0	0	دهک معیشتی	سرویس آنلاین دهک معیشتی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 19:16:12	t	8000	IRR	10000	\N	\N	\N	t	\N	f
436	ارزش سهام عدالت	justice-stock-value-inquiry	<p>ارزش سهام عدالت - سرویس آنلاین پیشخوانک</p>	11	\N	سرویس ارزش سهام عدالت در پیشخوانک	active	f	1	\N	2	0	0	ارزش سهام عدالت	سرویس آنلاین ارزش سهام عدالت در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:11:30	t	8000	IRR	10000	\N	\N	\N	t	\N	f
434	سوابق بیمه	tamin	<p>سوابق بیمه - سرویس آنلاین پیشخوانک</p>	10	\N	سرویس سوابق بیمه در پیشخوانک	active	f	1	\N	2	0	0	سوابق بیمه	سرویس آنلاین سوابق بیمه در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:58	2025-09-08 20:27:48	t	8000	IRR	10000	\N	\N	\N	t	\N	f
456	پلاک‌های فعال	platenumber	<p>پلاک‌های فعال - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس پلاک‌های فعال در پیشخوانک	active	f	1	\N	1	0	0	پلاک‌های فعال	سرویس آنلاین پلاک‌های فعال در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:58	t	8000	IRR	10000	\N	\N	\N	t	\N	f
461	کارت و سند خودرو	cardocuments	<p>کارت و سند خودرو - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس کارت و سند خودرو در پیشخوانک	active	f	1	\N	1	0	0	کارت و سند خودرو	سرویس آنلاین کارت و سند خودرو در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:59	t	8000	IRR	10000	\N	\N	\N	t	\N	f
466	گذرنامه	passport	<p>گذرنامه - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس گذرنامه در پیشخوانک	active	f	1	\N	1	0	0	گذرنامه	سرویس آنلاین گذرنامه در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:51	t	8000	IRR	10000	\N	\N	\N	t	\N	f
458	مالیات انتقال خودرو	cartax	<p>مالیات انتقال خودرو - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس مالیات انتقال خودرو در پیشخوانک	active	f	1	\N	1	0	0	مالیات انتقال خودرو	سرویس آنلاین مالیات انتقال خودرو در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:46	t	8000	IRR	10000	\N	\N	\N	t	\N	f
469	اعتبار معاملاتی افراد	transaction-check	<p>اعتبار معاملاتی افراد - سرویس آنلاین پیشخوانک</p>	1	\N	سرویس اعتبار معاملاتی افراد در پیشخوانک	active	f	1	\N	1	0	0	اعتبار معاملاتی افراد	سرویس آنلاین اعتبار معاملاتی افراد در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:14	t	8000	IRR	10000	\N	\N	\N	t	\N	f
462	گواهینامه رانندگی	drivinglicence	<p>گواهینامه رانندگی - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس گواهینامه رانندگی در پیشخوانک	active	f	1	\N	2	0	0	گواهینامه رانندگی	سرویس آنلاین گواهینامه رانندگی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:07	t	8000	IRR	10000	\N	\N	\N	t	\N	f
459	مالیات انتقال موتور	motortransfertax	<p>مالیات انتقال موتور - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس مالیات انتقال موتور در پیشخوانک	active	f	1	\N	1	0	0	مالیات انتقال موتور	سرویس آنلاین مالیات انتقال موتور در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:13	t	8000	IRR	10000	\N	\N	\N	t	\N	f
467	دریافت آدرس با کد‌پستی	postal-address-lookup	<p>دریافت آدرس با کد‌پستی - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس دریافت آدرس با کد‌پستی در پیشخوانک	active	f	1	\N	1	0	0	دریافت آدرس با کد‌پستی	سرویس آنلاین دریافت آدرس با کد‌پستی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:19	t	8000	IRR	10000	\N	\N	\N	t	\N	f
455	نمره منفی گواهینامه	nomremanfi	<p>نمره منفی گواهینامه - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس نمره منفی گواهینامه در پیشخوانک	active	f	1	\N	1	0	0	نمره منفی گواهینامه	سرویس آنلاین نمره منفی گواهینامه در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:53	t	8000	IRR	10000	\N	\N	\N	t	\N	f
460	معاینه فنی	technicalexaminationcertificate	<p>معاینه فنی - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس معاینه فنی در پیشخوانک	active	f	1	\N	1	0	0	معاینه فنی	سرویس آنلاین معاینه فنی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:21	t	8000	IRR	10000	\N	\N	\N	t	\N	f
457	بیمه شخص ثالث	bimesales	<p>بیمه شخص ثالث - سرویس آنلاین پیشخوانک</p>	10	\N	سرویس بیمه شخص ثالث در پیشخوانک	active	f	1	\N	4	0	0	بیمه شخص ثالث	سرویس آنلاین بیمه شخص ثالث در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:27:52	t	8000	IRR	10000	\N	\N	\N	t	\N	f
465	پیگیری مرسوله پستی	posttracking	<p>پیگیری مرسوله پستی - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس پیگیری مرسوله پستی در پیشخوانک	active	f	1	\N	1	0	0	پیگیری مرسوله پستی	سرویس آنلاین پیگیری مرسوله پستی در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:01	t	8000	IRR	10000	\N	\N	\N	t	\N	f
464	تاریخچه پلاک	car-plate-history	<p>تاریخچه پلاک - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس تاریخچه پلاک در پیشخوانک	active	f	1	\N	1	0	0	تاریخچه پلاک	سرویس آنلاین تاریخچه پلاک در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:15	t	8000	IRR	10000	\N	\N	\N	t	\N	f
329	استعلام اعتبار و محکومیت مالی - بانک رفاه	financial-judgment-inquiry-refah	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی بانک رفاه	\N		استعلام اعتبار و محکومیت مالی بانک رفاه	\N	\N	استعلام اعتبار و محکومیت مالی بانک رفاه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک رفاه	\N	t	\N	f
463	سوابق خودرو	carauthenticity	<p>سوابق خودرو - سرویس آنلاین پیشخوانک</p>	2	\N	سرویس سوابق خودرو در پیشخوانک	active	f	1	\N	1	0	0	سوابق خودرو	سرویس آنلاین سوابق خودرو در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:08	t	8000	IRR	10000	\N	\N	\N	t	\N	f
468	کارت پایان خدمت	military-card	<p>کارت پایان خدمت - سرویس آنلاین پیشخوانک</p>	12	\N	سرویس کارت پایان خدمت در پیشخوانک	active	f	1	\N	1	0	0	کارت پایان خدمت	سرویس آنلاین کارت پایان خدمت در پیشخوانک	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	t	\N	\N	2025-09-08 17:52:59	2025-09-08 20:28:03	t	8000	IRR	10000	\N	\N	\N	t	\N	f
313	استعلام اعتبار و محکومیت مالی - بانک دی	financial-judgment-inquiry-dey	<p dir="rtl">فثسف</p>	1	\N	\N	active	f	1	13	1	0	0	استعلام اعتبار و محکومیت مالی بانک دی	\N		استعلام اعتبار و محکومیت مالی بانک دی	\N	\N	استعلام اعتبار و محکومیت مالی بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک دی	\N	t	\N	f
2	تبدیل شماره کارت به شماره حساب	card-account	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	\N	53	0	0	\N	\N		\N	\N	\N	\N	\N	\N	\N	[]	\N	t	\N	2025-06-29 06:00:27	2025-06-29 06:00:27	2025-09-08 20:35:23	f	350	IRT	5000	\N	تبدیل کارت به حساب	\N	t	\N	f
79	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک کشاورزی	credit-score-rating-keshavarzi	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	1	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک کشاورزی	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک کشاورزی	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک کشاورزی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 20:36:00	f	8000	IRT	10000	\N	بانک کشاورزی	\N	t	\N	f
373	استعلام کد مکنا با کدملی - بانک ملل	inquiry-makna-code-melal	<p>test</p>	1	\N	\N	active	f	1	17	2	0	0	استعلام کد مکنا با کدملی بانک ملل	\N		استعلام کد مکنا با کدملی بانک ملل	\N	\N	استعلام کد مکنا با کدملی بانک ملل	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 20:19:28	f	5000	IRR	10000	\N	بانک ملل	\N	t	\N	f
38	تبدیل شماره کارت به شماره شبا - بانک دی	card-iban-dey	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک دی	\N		تبدیل شماره کارت به شماره شبا بانک دی	\N	\N	تبدیل شماره کارت به شماره شبا بانک دی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک دی	\N	t	\N	f
51	تبدیل شماره کارت به شماره شبا - بانک سینا	card-iban-sina	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک سینا	\N		تبدیل شماره کارت به شماره شبا بانک سینا	\N	\N	تبدیل شماره کارت به شماره شبا بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک سینا	\N	t	\N	f
52	تبدیل شماره کارت به شماره شبا - بانک تجارت	card-iban-tejarat	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	0	0	0	تبدیل شماره کارت به شماره شبا بانک تجارت	\N		تبدیل شماره کارت به شماره شبا بانک تجارت	\N	\N	تبدیل شماره کارت به شماره شبا بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک تجارت	\N	t	\N	f
65	تبدیل شماره شبا به شماره حساب - بانک مسکن	iban-account-maskan	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	2	0	0	تبدیل شماره شبا به شماره حساب بانک مسکن	\N		تبدیل شماره شبا به شماره حساب بانک مسکن	\N	\N	تبدیل شماره شبا به شماره حساب بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 19:07:12	f	350	IRT	2500	\N	بانک مسکن	\N	t	\N	f
92	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک سپه	credit-score-rating-sepah	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سپه	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک سپه	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک سپه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک سپه	\N	t	\N	f
107	استعلام وام و تسهیلات با کدملی - بانک توسعه تعاون	loan-inquiry-tosee-taavon	<p>test</p>	1	\N	\N	active	f	1	15	0	0	0	استعلام وام و تسهیلات با کدملی بانک توسعه تعاون	\N		استعلام وام و تسهیلات با کدملی بانک توسعه تعاون	\N	\N	استعلام وام و تسهیلات با کدملی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک توسعه تعاون	\N	t	\N	f
123	استعلام رنگ چک با کد ملی - بانک مسکن	check-color-maskan	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک مسکن	\N		استعلام رنگ چک با کد ملی بانک مسکن	\N	\N	استعلام رنگ چک با کد ملی بانک مسکن	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مسکن	\N	t	\N	f
124	استعلام رنگ چک با کد ملی - بانک مهر اقتصاد	check-color-mehr-e-eghtesad	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک مهر اقتصاد	\N		استعلام رنگ چک با کد ملی بانک مهر اقتصاد	\N	\N	استعلام رنگ چک با کد ملی بانک مهر اقتصاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک مهر اقتصاد	\N	t	\N	f
140	استعلام رنگ چک با کد ملی - بانک توسعه تعاون	check-color-tosee-taavon	<p dir="rtl">ندارده</p>	1	\N	\N	active	f	1	11	0	0	0	استعلام رنگ چک با کد ملی بانک توسعه تعاون	\N		استعلام رنگ چک با کد ملی بانک توسعه تعاون	\N	\N	استعلام رنگ چک با کد ملی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک توسعه تعاون	\N	t	\N	f
197	تبدیل شماره کارت به شماره شبا - بانک پارسیان	card-iban-parsian	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	1	1	0	0	تبدیل شماره کارت به شماره شبا بانک پارسیان	\N		تبدیل شماره کارت به شماره شبا بانک پارسیان	\N	\N	تبدیل شماره کارت به شماره شبا بانک پارسیان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک پارسیان	\N	t	\N	f
164	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک تجارت	cheque-inquiery-tejarat	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک تجارت	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک تجارت	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک تجارت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک تجارت	\N	t	\N	f
167	استعلام چک در راه - بانک اقتصاد نوین	coming-check-inquiry-eghtesad-novin	<p>test</p>	1	\N	\N	active	f	1	14	0	0	0	استعلام چک در راه بانک اقتصاد نوین	\N		استعلام چک در راه بانک اقتصاد نوین	\N	\N	استعلام چک در راه بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	5000	IRR	10000	\N	بانک اقتصاد نوین	\N	t	\N	f
215	تبدیل شماره کارت به شماره حساب - بانک سینا	card-account-sina	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک سینا	\N		تبدیل شماره کارت به شماره حساب بانک سینا	\N	\N	تبدیل شماره کارت به شماره حساب بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک سینا	\N	t	\N	f
216	تبدیل شماره کارت به شماره حساب - بانک توسعه تعاون	card-account-tosee-taavon	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک توسعه تعاون	\N		تبدیل شماره کارت به شماره حساب بانک توسعه تعاون	\N	\N	تبدیل شماره کارت به شماره حساب بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک توسعه تعاون	\N	t	\N	f
230	تبدیل شماره حساب به شبا - بانک سامان	account-iban-saman	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	0	0	0	تبدیل شماره حساب به شبا بانک سامان	\N		تبدیل شماره حساب به شبا بانک سامان	\N	\N	تبدیل شماره حساب به شبا بانک سامان	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک سامان	\N	t	\N	f
244	تبدیل شماره شبا به شماره حساب - بانک سینا	iban-account-sina	<p dir="rtl">تبدیل شماره شبا به شماره حساب</p>	1	\N	\N	active	f	1	4	0	0	0	تبدیل شماره شبا به شماره حساب بانک سینا	\N		تبدیل شماره شبا به شماره حساب بانک سینا	\N	\N	تبدیل شماره شبا به شماره حساب بانک سینا	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک سینا	\N	t	\N	f
246	استعلام و بررسی شماره شبا - بانک اقتصاد نوین	iban-check-eghtesad-novin	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک اقتصاد نوین	\N		استعلام و بررسی شماره شبا بانک اقتصاد نوین	\N	\N	استعلام و بررسی شماره شبا بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک اقتصاد نوین	\N	t	\N	f
268	استعلام و بررسی شماره شبا - بانک توسعه تعاون	iban-check-tosee-taavon	<p dir="rtl">استعلام و بررسی شماره شبا</p>	1	\N	\N	active	f	1	5	0	0	0	استعلام و بررسی شماره شبا بانک توسعه تعاون	\N		استعلام و بررسی شماره شبا بانک توسعه تعاون	\N	\N	استعلام و بررسی شماره شبا بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک توسعه تعاون	\N	t	\N	f
273	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک رسالت	credit-score-rating-resalat	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک رسالت	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک رسالت	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک رسالت	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	8000	IRT	10000	\N	بانک رسالت	\N	t	\N	f
289	استعلام ضمانت وام با کدملی - بانک اقتصاد نوین	loan-guarantee-inquiry-eghtesad-novin	<p>test</p>	1	\N	\N	active	f	1	16	0	0	0	استعلام ضمانت وام با کدملی بانک اقتصاد نوین	\N		استعلام ضمانت وام با کدملی بانک اقتصاد نوین	\N	\N	استعلام ضمانت وام با کدملی بانک اقتصاد نوین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	5000	IRR	12500	\N	بانک اقتصاد نوین	\N	t	\N	f
350	استعلام و دریافت شماره شهاب - بانک ملی	shahab-number-melli	<p>test</p>	1	\N	\N	active	f	1	7	0	0	0	استعلام و دریافت شماره شهاب بانک ملی	\N		استعلام و دریافت شماره شهاب بانک ملی	\N	\N	استعلام و دریافت شماره شهاب بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 18:49:20	f	2500	IRT	5000	\N	بانک ملی	\N	t	\N	f
204	تبدیل شماره کارت به شماره حساب - بانک خاورمیانه	card-account-khavarmianeh	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	1	0	0	تبدیل شماره کارت به شماره حساب بانک خاورمیانه	\N		تبدیل شماره کارت به شماره حساب بانک خاورمیانه	\N	\N	تبدیل شماره کارت به شماره حساب بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:20	f	350	IRT	2500	\N	بانک خاورمیانه	\N	t	\N	f
347	استعلام و دریافت شماره شهاب - بانک مهر ایران	shahab-number-mehr-e-iranian	<p>test</p>	1	\N	\N	active	f	1	7	3	0	0	استعلام و دریافت شماره شهاب بانک مهر ایران	\N		استعلام و دریافت شماره شهاب بانک مهر ایران	\N	\N	استعلام و دریافت شماره شهاب بانک مهر ایران	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:57	2025-09-08 19:37:40	f	2500	IRT	5000	\N	بانک مهر ایران	\N	t	\N	f
80	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک خاورمیانه	credit-score-rating-khavarmianeh	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک خاورمیانه	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک خاورمیانه	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک خاورمیانه	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک خاورمیانه	\N	t	\N	f
96	استعلام رتبه بندی و اعتبارسنجی بانکی - بانک توسعه تعاون	credit-score-rating-tosee-taavon	<p dir="rtl">ندارد</p>	1	\N	\N	active	f	1	6	0	0	0	استعلام رتبه بندی و اعتبارسنجی بانکی بانک توسعه تعاون	\N		استعلام رتبه بندی و اعتبارسنجی بانکی بانک توسعه تعاون	\N	\N	استعلام رتبه بندی و اعتبارسنجی بانکی بانک توسعه تعاون	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 18:49:19	f	8000	IRT	10000	\N	بانک توسعه تعاون	\N	t	\N	f
201	تبدیل شماره کارت به شماره حساب - بانک ایران زمین	card-account-iranzamin	<p dir="rtl">تبدیل شماره کارت به شماره شبا</p>	1	\N	\N	active	f	1	2	0	0	0	تبدیل شماره کارت به شماره حساب بانک ایران زمین	\N		تبدیل شماره کارت به شماره حساب بانک ایران زمین	\N	\N	تبدیل شماره کارت به شماره حساب بانک ایران زمین	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:56	2025-09-08 18:49:19	f	350	IRT	2500	\N	بانک ایران زمین	\N	t	\N	f
153	استعلام چک برگشتی با کدملی و شناسه صیاد - بانک ملی	cheque-inquiery-melli	<p dir="rtl">نداره</p>	1	\N	\N	active	f	1	10	0	0	0	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملی	\N		استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملی	\N	\N	استعلام چک برگشتی با کدملی و شناسه صیاد بانک ملی	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:09:55	2025-09-08 18:49:20	f	6500	IRR	12500	\N	بانک ملی	\N	t	\N	f
112	تبدیل شماره حساب به شبا - بانک پاسارگاد	account-iban-pasargad	<p dir="rtl">تبدیل شماره حساب به شماره شبا</p>	1	\N	\N	active	f	1	3	1	0	0	تبدیل شماره حساب به شبا بانک پاسارگاد	\N		تبدیل شماره حساب به شبا بانک پاسارگاد	\N	\N	تبدیل شماره حساب به شبا بانک پاسارگاد	\N	\N	null	null	null	t	\N	\N	2025-09-08 17:06:06	2025-09-08 20:28:26	f	350	IRT	2500	\N	بانک پاسارگاد	\N	t	\N	f
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.settings (id, key, value, type, "group", label, description, is_public, is_required, sort_order, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: site_links; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.site_links (id, title, url, location, icon, sort_order, is_active, open_in_new_tab, target, attributes, css_class, created_at, updated_at) FROM stdin;
1	درباره ما	/about	footer	heroicon-o-information-circle	1	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
2	تماس با ما	/contact	footer	heroicon-o-phone	2	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
3	وبلاگ	/blog	footer	heroicon-o-newspaper	3	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
4	حریم خصوصی	/privacy-policy	footer	heroicon-o-shield-check	4	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
5	قوانین و مقررات	/terms-conditions	footer	heroicon-o-document	5	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
6	صفحه اصلی	/	header	heroicon-o-home	1	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
7	خدمات	#services	header	heroicon-o-cube	2	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
8	خانه	/	mobile_nav	heroicon-o-home	1	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
9	خدمات	#services	mobile_nav	heroicon-o-cube	2	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
10	کیف‌پول	/wallet	mobile_nav	heroicon-o-wallet	3	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
11	سوابق	/history	mobile_nav	heroicon-o-folder	4	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
12	پروفایل	/profile	mobile_nav	heroicon-o-user	5	t	f	_self	\N	\N	2025-09-08 16:40:55	2025-09-08 16:40:55
\.


--
-- Data for Name: support_agent_categories; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.support_agent_categories (id, support_agent_id, ticket_category_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: support_agents; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.support_agents (id, user_id, agent_code, is_active, is_online, auto_assign, max_tickets, current_tickets, specialties, languages, working_hours, timezone, last_activity_at, response_time_avg, resolution_time_avg, satisfaction_rating, total_tickets_handled, total_tickets_resolved, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: taggables; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.taggables (tag_id, taggable_type, taggable_id) FROM stdin;
\.


--
-- Data for Name: tags; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.tags (id, name, slug, type, order_column, created_at, updated_at) FROM stdin;
1	{"fa":"\\\\u062a\\\\u0628\\\\u062f\\\\u06cc\\\\u0644 \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a \\\\u0628\\\\u0647 \\\\u0634\\\\u0628\\\\u0627 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"tbdyl-shmarh-kart-bh-shba-banky"}	\N	1	2025-06-28 10:28:57	2025-06-28 10:28:57
2	{"fa":"\\\\u062a\\\\u0628\\\\u062f\\\\u06cc\\\\u0644 \\\\u0633\\\\u0631\\\\u06cc\\\\u0639 \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a \\\\u0628\\\\u0647 \\\\u0634\\\\u0628\\\\u0627"}	{"fa":"tbdyl-sryaa-shmarh-kart-bh-shba"}	\N	2	2025-06-28 10:28:57	2025-06-28 10:28:57
3	{"fa":"\\\\u0627\\\\u0633\\\\u062a\\\\u0639\\\\u0644\\\\u0627\\\\u0645 \\\\u0634\\\\u0628\\\\u0627 \\\\u0627\\\\u0632 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a"}	{"fa":"astaalam-shba-az-kart"}	\N	3	2025-06-28 10:28:57	2025-06-28 10:28:57
4	{"fa":"\\\\u062a\\\\u0628\\\\u062f\\\\u06cc\\\\u0644 \\\\u0622\\\\u0633\\\\u0627\\\\u0646 \\\\u0648 \\\\u0633\\\\u0631\\\\u06cc\\\\u0639 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a \\\\u0628\\\\u0647 \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u0634\\\\u0628\\\\u0627"}	{"fa":"tbdyl-asan-o-sryaa-kart-bh-shmarh-shba"}	\N	4	2025-06-28 10:28:57	2025-06-28 10:28:57
5	{"fa":"\\\\u0628\\\\u0631\\\\u0631\\\\u0633\\\\u06cc \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u0634\\\\u0628\\\\u0627 \\\\u0627\\\\u0632 \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a"}	{"fa":"brrsy-shmarh-shba-az-shmarh-kart"}	\N	5	2025-06-28 10:28:57	2025-06-28 10:28:57
6	{"fa":"\\\\u062f\\\\u0631\\\\u06cc\\\\u0627\\\\u0641\\\\u062a \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u0634\\\\u0628\\\\u0627 \\\\u0627\\\\u0632 \\\\u0634\\\\u0645\\\\u0627\\\\u0631\\\\u0647 \\\\u06a9\\\\u0627\\\\u0631\\\\u062a"}	{"fa":"dryaft-shmarh-shba-az-shmarh-kart"}	\N	6	2025-06-28 10:28:57	2025-06-28 10:28:57
7	{"fa":"\\\\u0627\\\\u0639\\\\u062a\\\\u0628\\\\u0627\\\\u0631\\\\u0633\\\\u0646\\\\u062c\\\\u06cc \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"aaatbarsngy-banky"}	\N	7	2025-07-05 10:26:36	2025-07-05 10:26:36
8	{"fa":"\\\\u0631\\\\u062a\\\\u0628\\\\u0647 \\\\u0628\\\\u0646\\\\u062f\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"rtbh-bndy-hsab-banky"}	\N	8	2025-07-05 10:26:36	2025-07-05 10:26:36
9	{"fa":"\\\\u0628\\\\u0631\\\\u0631\\\\u0633\\\\u06cc \\\\u0631\\\\u062a\\\\u0628\\\\u0647 \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"brrsy-rtbh-hsab-banky"}	\N	9	2025-07-05 10:26:36	2025-07-05 10:26:36
10	{"fa":"\\\\u0628\\\\u0631\\\\u0631\\\\u0633\\\\u06cc \\\\u0639\\\\u062f\\\\u062f \\\\u0627\\\\u0639\\\\u062a\\\\u0628\\\\u0627\\\\u0631\\\\u0633\\\\u0646\\\\u062c\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628"}	{"fa":"brrsy-aadd-aaatbarsngy-hsab"}	\N	10	2025-07-05 10:26:36	2025-07-05 10:26:36
11	{"fa":"\\\\u0631\\\\u062a\\\\u0628\\\\u0647 \\\\u0628\\\\u0646\\\\u062f\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc \\\\u0628\\\\u0627 \\\\u06a9\\\\u062f\\\\u0645\\\\u0644\\\\u06cc"}	{"fa":"rtbh-bndy-hsab-banky-ba-kdmly"}	\N	11	2025-07-05 10:26:36	2025-07-05 10:26:36
12	{"fa":"\\\\u0628\\\\u0647\\\\u062a\\\\u0631\\\\u06cc\\\\u0646 \\\\u0631\\\\u0648\\\\u0634 \\\\u062f\\\\u0631\\\\u06cc\\\\u0627\\\\u0641\\\\u062a \\\\u0631\\\\u062a\\\\u0628\\\\u0647 \\\\u0627\\\\u0639\\\\u062a\\\\u0628\\\\u0627\\\\u0631\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0647\\\\u0627"}	{"fa":"bhtryn-rosh-dryaft-rtbh-aaatbary-hsab-ha"}	\N	12	2025-07-05 10:26:36	2025-07-05 10:26:36
13	{"fa":"pdf \\\\u0631\\\\u062a\\\\u0628\\\\u0647 \\\\u0628\\\\u0646\\\\u062f\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"pdf-rtbh-bndy-hsab-banky"}	\N	13	2025-07-05 10:26:36	2025-07-05 10:26:36
14	{"fa":"\\\\u0627\\\\u0639\\\\u062a\\\\u0628\\\\u0627\\\\u0631\\\\u0633\\\\u0646\\\\u062c\\\\u06cc \\\\u062d\\\\u0633\\\\u0627\\\\u0628 \\\\u0628\\\\u0627\\\\u0646\\\\u06a9\\\\u06cc"}	{"fa":"aaatbarsngy-hsab-banky"}	\N	14	2025-07-05 10:26:36	2025-07-05 10:26:36
\.


--
-- Data for Name: tax_rules; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.tax_rules (id, name, type, rate, is_active, is_default, applicable_currencies, min_amount, max_amount, description, sort_order, created_at, updated_at) FROM stdin;
1	مالیات بر ارزش افزوده	percentage	9.0000	t	t	["IRT"]	0	\N	مالیات بر ارزش افزوده ۹ درصد	1	2025-09-08 16:40:52	2025-09-08 16:40:52
2	عوارض خدمات	fixed	1000.0000	f	f	["IRT"]	10000	\N	عوارض ثابت خدمات ۱۰۰۰ ریال	2	2025-09-08 16:40:52	2025-09-08 16:40:52
\.


--
-- Data for Name: telegram_admin_sessions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_admin_sessions (id, admin_id, session_token, ip_hash, user_agent_hash, expires_at, last_activity_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: telegram_admins; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_admins (id, telegram_user_id, username, first_name, last_name, role, permissions, is_active, last_login_at, failed_login_attempts, locked_until, created_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: telegram_audit_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_audit_logs (id, admin_id, action, resource_type, resource_id, old_values, new_values, ip_hash, user_agent_hash, success, error_message, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: telegram_posts; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_posts (id, title, content, status, scheduled_for, published_at, channel_id, message_id, created_by, updated_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: telegram_security_events; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_security_events (id, event_type, admin_id, telegram_user_id, ip_hash, details, severity, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: telegram_ticket_messages; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_ticket_messages (id, ticket_id, user_id, message, is_admin, created_at) FROM stdin;
\.


--
-- Data for Name: telegram_tickets; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.telegram_tickets (id, user_id, user_name, subject, status, priority, assigned_to, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_activities; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_activities (id, ticket_id, user_id, action, description, old_values, new_values, is_public, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_attachments; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_attachments (id, ticket_id, ticket_message_id, filename, original_filename, mime_type, file_size, file_path, created_at, updated_at) FROM stdin;
1	4	4	1757350711_IMG_1037.png	IMG_1037.png	image/png	147042	tickets/4/1757350711_IMG_1037.png	2025-09-08 20:28:31	2025-09-08 20:28:31
2	4	6	1757350788_IMG_1037.png	IMG_1037.png	image/png	147042	tickets/4/1757350788_IMG_1037.png	2025-09-08 20:29:48	2025-09-08 20:29:48
\.


--
-- Data for Name: ticket_categories; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_categories (id, name, slug, description, color, icon, is_active, auto_assign_to, required_fields, estimated_response_time, sort_order, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_escalation_rules; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_escalation_rules (id, name, is_active, category_id, priority_id, trigger_after_minutes, trigger_condition, escalate_to_priority_id, escalate_to_user_id, send_notification, notification_message, sort_order, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_messages; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_messages (id, ticket_id, user_id, message, is_internal, attachments, created_at, updated_at, template_id, is_system_message, message_data, read_at, message_type, is_auto_response) FROM stdin;
1	1	109	با سلام من 100 هزار واریز کردم و در تراکنشها اعلام وصول شده ولی کیف پول صفر میباشد و نمیتوان اعتبار سنجی انجام داد لطفا راهنمایی کنید	f	\N	2025-09-08 18:36:09	2025-09-08 18:36:09	\N	f	\N	\N	text	f
2	2	178	وقت بخیر\r\nمن استعلام بیمه ثالث ماشینو میخواستم بگیرم\r\n۱۰۰ هزار هم کیف پولمو شارژ کردم\r\nپولم رفت\r\nاستعلامم هم به دستم نرسید	f	\N	2025-09-08 18:41:00	2025-09-08 18:41:00	\N	f	\N	\N	text	f
3	3	181	با سلام،هفته پیش یکبار بعد از سرچ در گوگل و وصل به سایت شما، برای درخواست استعلام اعتبارسنجی بانکی، صد تومن واریز کردم و از حسابم برداشت شد،بعد سایت پیام داد که هنوز ثبت نام نکردین،،،بعد ثبت نام دیدم حسابم صفره. شما که اول ثبت نام براتون مهمه چرا قبل از واریز اعلام نمیکنین؟ این هیچ ،دوباره بعد ثبت نام صد تومن ریختم و یکبار استعلام گرفتم.الان که دوباره خواستم استعلام بگیرم،دوباره حسابم صفر شده و سابقه تراکنش دوم رو هم نشون نمی ده!!!! آنقدر هم ازش گذشته که برای گرفتن پرینت حساب و ارسال مدرک باید برم بانک و نمیصرفه اصن وقت بذارم. اگه باگ سیستمی هست درستش کنین اگه هم سر گردنه نشستین که جور دیگه اقدام کنم. با تشکرررر	f	\N	2025-09-08 19:12:15	2025-09-08 19:12:15	\N	f	\N	\N	text	f
4	4	39	واريز به حساب منظور نشده	f	\N	2025-09-08 20:28:31	2025-09-08 20:28:31	\N	f	\N	\N	text	f
5	4	39	پول به حساب بنده واريز نشده	f	\N	2025-09-08 20:29:04	2025-09-08 20:29:04	\N	f	\N	\N	text	f
6	4	39	كيف پول را شارژ كنيد	f	\N	2025-09-08 20:29:48	2025-09-08 20:29:48	\N	f	\N	\N	text	f
\.


--
-- Data for Name: ticket_priorities; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_priorities (id, name, slug, description, color, level, is_active, auto_escalate_after, escalate_to_priority_id, sort_order, icon, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_sla_settings; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_sla_settings (id, name, is_active, category_id, priority_id, first_response_time, resolution_time, working_hours, exclude_weekends, excluded_dates, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_statuses; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_statuses (id, name, slug, description, color, icon, is_active, is_default, is_closed, is_resolved, requires_user_action, auto_close_after, sort_order, next_status_options, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ticket_templates; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.ticket_templates (id, name, slug, subject, content, category_id, is_active, is_public, created_by, variables, usage_count, sort_order, auto_close_ticket, auto_change_status_to, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tickets; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.tickets (id, ticket_number, user_id, subject, description, priority, status, category, department, assigned_to, resolved_at, closed_at, response_time, resolution_time, created_at, updated_at, category_id, priority_id, status_id, custom_fields, escalation_count, escalated_at, escalated_from_priority_id, first_response_at, customer_satisfaction_rating, customer_satisfaction_comment, tags, is_auto_responded, auto_response_id, auto_responded_at, ticket_hash) FROM stdin;
1	TKT-2025-000001	109	درخواست اعتبار سنجی	با سلام من 100 هزار واریز کردم و در تراکنشها اعلام وصول شده ولی کیف پول صفر میباشد و نمیتوان اعتبار سنجی انجام داد لطفا راهنمایی کنید	low	open	billing	support	\N	\N	\N	\N	\N	2025-09-08 18:36:09	2025-09-08 18:36:09	\N	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	f	\N	\N	TKT_ZFWH44EUGH6UK4W2J0P3ROBS
2	TKT-2025-000002	178	استعلام بیمه ثالث	وقت بخیر\r\nمن استعلام بیمه ثالث ماشینو میخواستم بگیرم\r\n۱۰۰ هزار هم کیف پولمو شارژ کردم\r\nپولم رفت\r\nاستعلامم هم به دستم نرسید	low	open	billing	support	\N	\N	\N	\N	\N	2025-09-08 18:41:00	2025-09-08 18:41:00	\N	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	f	\N	\N	TKT_ZQ9FZOY7IRJZH962II8I9JTM
3	TKT-2025-000003	181	عدم مشاهده موجودی	با سلام،هفته پیش یکبار بعد از سرچ در گوگل و وصل به سایت شما، برای درخواست استعلام اعتبارسنجی بانکی، صد تومن واریز کردم و از حسابم برداشت شد،بعد سایت پیام داد که هنوز ثبت نام نکردین،،،بعد ثبت نام دیدم حسابم صفره. شما که اول ثبت نام براتون مهمه چرا قبل از واریز اعلام نمیکنین؟ این هیچ ،دوباره بعد ثبت نام صد تومن ریختم و یکبار استعلام گرفتم.الان که دوباره خواستم استعلام بگیرم،دوباره حسابم صفر شده و سابقه تراکنش دوم رو هم نشون نمی ده!!!! آنقدر هم ازش گذشته که برای گرفتن پرینت حساب و ارسال مدرک باید برم بانک و نمیصرفه اصن وقت بذارم. اگه باگ سیستمی هست درستش کنین اگه هم سر گردنه نشستین که جور دیگه اقدام کنم. با تشکرررر	low	open	billing	support	\N	\N	\N	\N	\N	2025-09-08 19:12:15	2025-09-08 19:12:15	\N	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	f	\N	\N	TKT_WWQPARGLWQHWEJNY2JIZBODQ
4	TKT-2025-000004	39	واريز به حساب منظور نشده	واريز به حساب منظور نشده	low	open	billing	support	\N	\N	\N	\N	\N	2025-09-08 20:28:31	2025-09-08 20:28:31	\N	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	f	\N	\N	TKT_OUEOTVUVEAKPNSBB0HANIIMA
\.


--
-- Data for Name: token_refresh_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.token_refresh_logs (id, provider, token_name, status, trigger_type, message, metadata, started_at, completed_at, duration_ms, error_code, error_details, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tokens; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.tokens (id, name, provider, access_token, refresh_token, expires_at, refresh_expires_at, last_used_at, metadata, is_active, created_at, updated_at) FROM stdin;
1	finoservice	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJhODQyYTE1Mzk1MDM3YmE5Y2ZkN2E0YTg0ZGFhZjg5N2UxMDA3NTRiYWZmZDk1ZDFiMjk2MWE3MzdmNGQ0ZjczNDI3NDllMDBkYzkwZTBmNzRiNjVlNzcwNWUzNDljMzY3MzA0YjE5MWI3MDdhYzcxN2YxMTNiNWYwZDU5ODk5ZmU3ZjY4MGE3OTZlOTRmZjZiM2RiZDdjMjFiOTU5OWE2MmQ3Nzk2NmFlYjMxYzMxY2VmZTBhZmYwZTNlOGIxODdlZDcyNmNlODhiNGU0MDBkZDk5YWQ4MzA4MjY5NDNiZWM1OTRhYWNiZTg3MmQyNjk3YmQ5MDI0ZGI2MzYwYzExMmIxOTc5MDM3OWU4OTRkNDgxNjJmYmIzYTA4ZmQ5NDUyZTU4NGY5ODJkMmI4YjEyNWZhOTM3NWI4NGM1MGI2MDQ2MTI2ZGVhNTM0MzkxYmRlZTJiZjg0YjQ3ZTg4OTg1NWNkZjc0OWQ0ZTYyNTBiMGU0NjEzNzZlZmFkZDE1ZTliYjZhNDU1YmUyZWY1ZTFiM2E5N2UyYzJhYTFhMjMwMThiMjcxMTUxOGQwMWQ2YTU3YzdmMzY4YzdlMDRjZTM3NGM1ZThmZjhmYjMyZWNhYjc3ZTFhNDg4ZTJkZDNjMjlmMDdmZTA0MWI3ODJmZGI5NGU5ZWJjY2RiMGI5YmI5ZDE2NjUzNjU2OTc5MDQ2M2E2ZmIwYzNkMTYyODQ4NzYyZTViZDQxZDIwY2E5ZDQzMzRjMWUzNjA0NTEyYzQ1ZmExNDY2OTYyODMzMzg1NjNiMjA2NzJlNjkzMWY4MTQxMTZiZDFhMzViMmY5M2YzNjZhYTA2YjViNGVjOWEzZjBkNTY1MTZhZWFjNDhjYzMwMmQ5MDIxZTlmM2VlY2I2MmVkNGUxMzJjZDNlODg1NDg4NDk1ZmE0YjRkZTdkMzJiZGJjMmI3OGNhNTE1ZmVhOTg1ZjU1NTcxMmE5NGU3ZTkyMmYxM2RmNTRkNzcwMzQwYzQwMDdmODNkNmZiODY3ZWVmOGQwYTExZTY2MGI0M2JhMTAxMmQ1YTM1NWExYWY4YjE4ZmM3YzVlMmMxYzc4Mzc3NmJkODA0MTMwYjQxNTMwYjc1NDVkMWQ0YzE3MjM0ZmI4Mzg3MmFkNjBiZDAwZjM1ODZmNGUwNmFmN2FkMWI4YmZjNDMwMjMxY2VmYjExZTIwZWVjMmNkMzQ1ZTljNmQ4ZDdmM2JkOGQ1MzQ1ZGJkMTgxOWQ4Mzc3YTUwMjQxMTQyMmM3NDZiZmU3YWU3NGIwOWUwZGM1ZGNlOTA1YzYyN2Y0ZjIyYjQzZDM5YmJjZGY2ZDIzYWM2NzFlMjNjMGMxOGUxYTFjOTgyZWY3YmVkNjIxNTYyYThkOTYxNzIwN2VjNmQ4NzA0ZTA5YmI5NTdmNWJlMDRmODE0NWYxMzU0MmFmNTk4ZmVmZWE4ZWU1ZDM2ODVjNTgxY2E5NmQ0ZjgxYTdiMmUyNTI0NTA0MzM2Nzk1NzhlZDg3MWVhMzZlZTgyMzQwMDc3OGZkN2FkZjhhOTVkOGQ0ZjNlNmU0MDgwNzY2Y2RhM2Y3MzA2ZWVlODBjNmM4ZjRiYmY0OGVjNGE2ZTJlMzJiM2E4MWYzYTlkMTYzYjdlNTBiZDMwZmNmNDQ5YjUxMWE3MzI5YzU3M2U4ZGFkNjdjNTQwNTdiODE1NjBjMDg3MTJlZDhhMTE5NzdmYzJiOWY2MjNlMmY4ZWRhYjRjZTQ3Y2NjOTE0MWU5YjRmYTNjZGFkMGZhMWUxNGFmYmJjYzViYzA2ZTAzMDI2ZmU2Y2NkNDUxMGQ3ZjA2MDBkODQyN2RjNTc2NzAyOTNhZDI2MTYyYTMzYmMzMGE0OGUzMWRhZjUyMDNlYWUzODc4YmM1ZWRkNzEzYzI2ZjdkOTQ4YmE4OGE4NjMzMWRmOTc4NzM1MWU0MWQ2NjMwOTc4ZTM4MDBlYzRmMTUzN2EyZjViNmRiOTAwNTgyM2I4OWY3ZjRlMjA0ODAwMWUzMjQ1NDMxNzUxYmU1NGYxOTlmNDhlZjRmMDc4MDM4ODlhMTZjZjhiNTFiYzk3OWE4NjAyNGQyMmEyNzYzNGI4MjU0MWM0NmU4MTFiMGU5NDY0NGU5M2JhODhjZjcxOTNmMDA0M2Q5MzBjNGZjNTQ1ZGZjZTM4NzhmYTM5YzllYzhkNzUwN2EzN2M0MDQ1MjczYjg4NmFkODg5MGI5OTdiYmQzNjc4YTY4YjEyZjM2ODM4OTI3MTNjOGUyMDQ3NDUwMTMwZmYyMGZjODVlOWY1MzZiYzllYWQ4MDhkYzBlZmE2OTVlMzBlZDY0MzQ4ZmQ1MzY0OTYxNjhkM2U5MTkzMGZiYjFhYjAzZTdmNzZhNzhkODZlYTY3Y2VkMTQyYTc4MTRkNDc5OGQ3ZmQ2YmZlYTY1N2UyNDU0ZDAwN2E4YmY0ODM0MGU3YmY3YjkxOTBmM2VmOGY5YjBlM2E3MGM4ZTQwYTMzOGZlYTRkODkzODc5NjQzNTk1NGUwOWUwMzBiNjI5MzVjYmZkODVjNGU1MTliMWIzNWYwYzBmNzAyM2I1MzcyOGRkNzgzOGJjYTM5NzkwZjcyMmFlNGJmMzA1MzRjZTllMTY3MmM0ZDJmNmY3Yjc0OTg3ZDM5ZGZjOWVlYTJhMzFmM2UxZmNhMGE3NzIwOGVhNjk1NzMyOTk1NzZkYzNiZDA0ZWJmZDM4M2I0NGJhNTY3YWUwZWI3NzJhYTBjNDNmYjM3OGU0YTYwYzY3NGEzMTM0NTk2YTNkMjEwODJmM2E5ODQ4MGJiMmFmMjIwZDg3ZjhhNmVkNmNjMjZhOGY1ODAyMGNlZDUzNTZiYjQxMWJhODE3ZDQ3YWZlY2Q1ZTBlMWM4NTZhNDVmYmU4ZDA2OWI4NjM1ZTNiODZiYzBmNjA0ZjJkOTM4MTAzZTRjNTE3OTQxODk1YjYzNDAwYWU1NjI5OGNhOGNjM2VhNWM1MzZhNWEyYzE3YjBkMjVkZTBmZjViNjQwYTM4NzZmNWVlNjcwYTRhNThjM2JjZjUzNjhjOTFjODJmZWFkNGM2ZmY2MjYyYjg2ODJiZWY0ZDg2MzQ0ZGQzNDkyMWY1NDYzYmZhNTA2NzhiNjdiMGRhZjdmNDI1MmVmZWQwMTc2MDJmN2YyYjk2ZDhjODM0NzI4YzFlZDRjMTAyNmMxNzIyYmQ1NzU3ZTFiZTgzMTU3Y2FiZDM0NDdmYmE2YTk0NjQzNWJjYzk1YmUwMjVjOGQ2ZmZmMzYxZWVkMTAzNzEwMDU1NGI2ZWNmYzBkM2YyYjZhYWU2ZmNmZjVjNDM0ODNiMjVkNjU1OWQ2NmM0MTVhNmUyNmYzNWY5OTcyMDgxYTA1ZWQxNjljYjk2YjEyOGYwNDdiY2RjMTljMWJhMDM5NTcyMjkwNjRlODMyMjdkZWRhNTgyNTY3ZGY1YmUyNThmZmVlY2UzYTgzOGYzNTBkY2FhYmVhZjBiNjNiZWU0NzE1ZTcwOGI4MTExNDA3YTJmM2NkNjAyNWZlMjBmZjgxNzM1ZDAxOGQxZTIzOGJjMDFmYWRmMDkxZmU3NTMzMDE5ZjMyMjBhY2ViMjMwMmFhMzVhZGUzNmI0M2U1ZjgwMGNhMDYwMTdhMTg0YjBiN2NkYWU3N2FjMzU3MGQzNTRiMzQzOTY2ZDBmNjRjY2MyZDljZTdjZGNiMGYzYjYzMGUzYTA4NGY0ZDViNTFhNTA2NGE1MTYzYjZmYzczMzU4YzYyN2VkM2M0N2M5YWYxNTRiMWE0MjAyNDhiM2U4NzJjYzA3NmExNWJlODVlY2VlNmRkMmQzNGM1MzNkNTE2OGIzNmI1YTAwNTFlOWQ1Zjg5NjBjMzRmZjFlODFlYWI4MGM1MGRhNjYxNGZjNGJlNjc5OGNhNzg2ZWRjNGFmZDA5ZmVkNTU5Y2IxNDE5MzBiOGZiN2Q5NjNhODJjYjBiODcwY2E4ZWYzMGQxYmY2MmY2ZGJmY2RhNDZlZTYwYzU1ZGNlNDZhNGRjMTVlMDcxOTk4ZThlNGJkNDhjNGY0YTUzMWJlN2E0ZGUxZWJkZTFlMzIyNzJiMWI3Njg4OWNkMDFhYTEzNDFmMDI0ZWYxNDhmYjhlNzM1NTU2Nzg0MjEzOTFkOTM4MzEwZTJkMWE0MjI3MjhkZDAxNTc2MmVlYjRmNmFlNDRkYWJmYzJmMTlkYTQ5YjMxYjlkNjljZTgzYTkwYzcyNjc3MzQ0OTgxYzRjMzA3ZTQyZmQ1ZjJhNDVhOTVhZjg4NjIyODM3NDMxNjAyNmIzNTlmMTBmMDEwNGE2MzgwYzcyZjBhY2Y3ZGIzYzE1NmI5YTM0MzNmYWVlYmYzNzQxMThmZDYwOGVmNmJmMDY0OTk2YWI2MWRkMzAyOGExZDI5ZWJjMjQ4YTg0MzZmZGQ0NzUzZjBjMWM4OWNjYWJlN2NjMTZmNzA1ZWI0NGQ2YWNjYTU4MTA2N2Y3OWE1NWVkN2FhYmZjN2MyY2Y0M2I5ODliOTQ5MzNlNmEwNzFkZmEzNDU5MDcwMzI5MjJlZjZkZmM1ZGI5NDI5MzU1YTMyMTE0OTJjNGYyN2I5NDNhY2IwMDI5Nzc2NTM2Y2JlNjc3OGQ4MDcwMWZhYzQxNmE4MjZjM2Y5ODU5OTQyYjRmNWJmOGJlYjNmODVkNWY2YzgzNWJkMjcwNTRhYTYyM2EzZmUyMzRjNDM2OTU0ZGIzZGQ0YjUzMjk4ZjQ4MmVmOWRmN2I2YzI1NTNhZDc3Y2MwNzc1MjZlYzE4MjUzZGM4ZTM5YTQ5OWRlMzY3MWFlYTM2NTI4NTdhYmQ0NWYxOTdlNjdhMWEyYmUxOTYwNTA3ZTk0N2I1Mzk1MGYwYTE1MWNjMzQxODMyMDhkYmUxZDM1MjNiZGM4OGZiMzk1Y2M0YTkxNzQxNWFmOTAyMjU2NTFlNmMxMGNmZTdjMTM2Y2RkNGNmYjE1MjM4NTk2M2VkZWQ1ZWIyZWUxNTAwOGMwNjUzZTlkY2VjZTFiZjMzZDQ2OWY0NjEwYzJkMjE3MjEwMjM1MjljYzYxYzkyZTA4OTEyMjViY2VkN2NhOGVlNTQ3YzcyMjgwMGI2NWMzYzkyNTgwOTY2Zjk1ZWM0MDI1MzIxMmMxMzhlMzI4ODc1ZTA0NDgzOTIyNDg1NTg4N2EzOTk3MDM3OGMxZTU0NDRhZWVmODg3ZjUyYTcxMzY5YjRjMTljMjE4Y2U4MWRhZDBkYjM2YWI4ZDc3MDI5YjQ1MzBkMGJiY2U1MDg5NGFkMWI2OGVhZjk2MzYzMTNhYzRhZjk3MzY3NzM3MjA2MWQ4MDgyZjc1MTIxNWFiYTY5NjEwYTdiNDQ2ZTk5MmRmOGE2YmZjMDYxM2JkMWE4NTM2OWM4MGNlZTQ1OTY2ODQwNjc5Zjk2NjM0ODRiOTRhZWVhMDYxYTA1NzY0OGRhNWYxZGY2NTZhMTQyNjRiNzQ0MWIyMmUzZTFmOTM3NmEyMGY4Y2IxNDVhNDBkZjcxMTdiMTM3YTMzNGY0NTEzMDJhMzVmMzEzYWNjZTM0ODk4ZGIwYjRkZTY2Nzc1OWYyMTFhYzU4ZDE1M2QzN2FiYzJmZGMwNzY1YjE2ODQzNjE1OTc0NzRlZGUwMmE3NGEwMjU2NTJjMDk2MGI3MWY2YTliN2VjNjc0OTY2OWJlODUxOWM2OGE0MDcyMTY4MjU5NGU4YjdiNDllM2E1MWY4MmNiZmM3NTM0ZGZmYTNmNGMyNjk0ZmNmODExM2Q5NzFlNDBkYTA2YTY1NWEyYjgwM2E0N2U1MzJhMzQxOTQ2Mzg1YmY4N2JlMjllNWVhNjkxMWUwMmQzZjE5YWExYWI5NWE4MDhiY2UyNGRkMWFlYTRiYTVlODBhNTEyYTUwZjEyMGZjM2ViODhmMzhiNjE3NWVhMTRlMTE4MzE3OTFhYTI0MzAwYThlYjkzYTIzNTBiOGI2MTFhMDMzNTk4MjhjMjY4ZDA4MDU4OTAzMDk5NzNjNTcyNjk4ZWYzMmVhYjJiNjUzYThhZDA3MjJkNDI0MWY5NWQ4ZTQ2MWY1ZGE0ZTMwMTI1NTFjNWY0YzM2N2Y3ZmE3YjcyZDg2NjhmZjMwODY0MzFmZTMxZGQ3OGJmMTJjNTM5YWM4NDI5YTcxNDE0ZDk5YTYxNDFjYTU5ZDVhZDc2NjFlOTRlZDZjNjkwNzRiZWU1M2E0Y2VhZjk3ZGMxNTY2YzA0NjY2NjUwODA0YjQyNzY5NDIzZmI5YTY0MjM4NmU5MjMwYzU1YjdmNWU1ZGQ5YThmMzZlMzM5YzgzNTdhMzA0ZDUzMzk5ZjQ2OTk2YmU3ZDI5N2I3MmIxMTEzMmIxZTVkOTBhZjEyODY5ZDI0ZmIyNjAzODBlMTQxM2VjYzUzNTQwMmRhY2VkOGE2MjMyMDVhOGRlODQ5MzI5ZjlkODllNTU0MjM1ZmE5NjBjYTBhNjEyZmM1MGRjNzM3NzFmNGVjN2M0OGJiZDNmMTA0ZjNiNjg4MzE3MWJmOTJhZTIwOWFmOTcxMTgxZGZlOTA4MzdhODY1MDkwZDk2MGNmZjQ5ODI2YTMwY2VjNzYyMjM2YmY3ZjcyZWMyOTFkNmRlOThjZGQxODIxZjc2NTI0ZDQ2ODYzOTRkYTg1ZmJiZjViZDk3MDEzZjY4MzQyMWU3MTNmOGM4MzFlNDk1NWVlN2U5YWY4ZTkwZTNhZDA4YzY2ODc4MGFjNGQ0NGIzMGNiNWRlMTI2YWM3MjRkZDRjOTc5ZWVlM2Y2ZmFiYjEyODc3ZDdlNWQ1ZmM1OWRjMDEzZTIyM2FkNzRmOTcyY2EzMmNjNjMxZmI3NDczNGJkYTA2NGE4OWUxODBhM2ViZDhkMjMyYmI2Yzg4NTAxYjE2MDliNmUyZjUxZmUwNjIxOTkwZGRlZDE1NWQ1Y2ExOWY3NzBhZjM1NjAyM2M1MDg3ZGEwN2I4YmZlYmYyOGMzNTBlMjBkZDI3OTQyM2JiYjViNjg0ZTg5YmQxNTkyODkxYWVkM2MxYTcxNjI1Nzg5NmFjOTA5MDhkNDYyN2Y3ZTIyNjg3N2M0NTgzMmEyNmI0NjY5ZDA3NDNkMTQyYTBjNWUyYTY2NTcxMTE3YjBlZjU3Yzk4ZDFmNGNkN2I5M2U3YjQyMmQ4NDhmZGRlNzBjMDAxNGVkMDRkYjE1ZTljMWRjOGNmYjA4YTEyNmFjYzA0NDBjOTg2M2E1ZmE3NzJkNzBhZTgwNjQwZTQ1MTI3ZmEyZTQ2Y2Q4NWMxMTQ2NWNmZDI4M2NkMjU4MDcxN2Q3ZTdjMDhmMzFjNDdjYzY3YzBjZjRkNTMxYjlhYmU3OTgwZjY5NTAzODEwYTJkODM2NmFlMjhlMmFhZWJiYzBlYzgyYzhlYTU4MDU2YmFiNDBjMTlhYTYxMWRiNTE1MGE4ODM0ZjBiNDU5Y2FjYjNlYTUwYzc4YmRlMjQwMzBkYjFlMWI2MWUzODhmNjk5ZDkzMDU1NmU1OGZjMDQ5NDI0MTQ3MmY1N2VkYjc1ZGE0NWY0OTFhYWNjZTdhN2Q5OWNmMGQzN2IyNjNjZjA3ZTJjNWM1OTJlN2ZhYmFkNDBjN2U3NmZmNjc2ODM2NGNkNDc4NGZkYzg1YzZhYzkwZDQzMzM3N2E3ZTYzMjU4MzA0NWUxZTgxYjZhN2I4NjVkZWUxYjlkZGEwYWUzMzI1MDg1ZjNkYTcwNTc2ODQzZmY3MDg4MzExZjM5OGM4M2NkMDFiMTQ0OWU0NzUwZThkYzU4NDc4NjdiYzk3M2FlOTYzY2IyZmUzYTczMTEyYjcyYjI3OTI0NGQ2Zjg0ZDJhYzczYWE1NTRlNmE3YWExMTE1YzI5OTY0ZDFlODY2ZjQyOGVlNjM4NzMwMWIyZGQyZTdkYzI0NGNlOTBkMjFmNTIwM2IwNjFiMGMwOGQzZGNmZDI3ZDkwMTcyZDcxNTQxZmQ5ZThmODY2MzU3MGM5NzQyZWZjYjU4N2ZkNWI0ZDJiYzQ5ZWQ1ODc4ZjBmOWY5MDNhYTMyMDBjNGU1ZTFmMWI0OTQzMDUzN2RkZTQ2OWU3OWRjODllNGIwZDc2ZTdkOWViNWUzMDgyMGM1ZWE4NGJmMjM3N2ZhZjM0NDA3OWFkMDIwOWEyNGFlYjAwNjRmYzBhOTI5Y2QwYmY5NTNmMjE5MjU0OGNiZWRmZDM5YTk2MWY2ZmVjZTYxODFmZDQzNDQ1NTFlN2E5ZjJjOTc5ODA2NjI3YWE4MjYwYWMyZmRlMzY1YWNkYWNkMzg4MDhkZGE0ZTVkYjA2ZGRkYjJhOGQwYzcyOWFjNGU4NzU4NDE5ZTFmODAxZTI1MjgwOGFkOWI3ZGVjOWQwYzA0N2NhZGQ4NjBhNTg3MGViNGM4YWEwNGYwNWUxZjEwM2E2MjIwOTI1ODMzZjQ3MDQ0ZWZiMmYyYzc2YjMzNDI0YjY3YzAyNDZmNjFiZjVhNjliN2JhZDYyMjljMjczMTVhODgyZTM4ZTE1ZDMzZGU4ODZjMjMwMjRkN2VlYzY5M2Q4ZmU0Njk4MTc1OWExMjk0NDQ5MWM5MDk3Y2IxMzdkNzcyMWJjMzBiYjc5OTEwNzVkNjUxNGIyYzU3ZjRlNmVhZGQwNzE1YTMxMzAzMGVhMGEwNWM2NGUyODZkM2I2NGU2NDFmYTZkNGJlYTYyMTNmMzVmOGQ1OTc1ZmVmNDU5MjMyNDU3MGExYjFhY2JkMjcyYjFjY2JjNjZiZWI2ZWUzMTgwZDY5NjA0NDFhMzM5MDllZjBhNzE5YWViOTQ2ZGJlZTNhMWQzYTkxMDY5MmJmM2M1ZDg5Nzk5YmE3ZmE2OTcxNDA0Y2JmYTE2MTM2ZjkyYjRlYzE0OTc2MTNkM2Y1Y2VjMjIxMjdlMTdhMWNmNTFmOGZlOGUxMDNlNjk0YzI5MjVkZmI5YzA0MDM1ZjcyYzI0NWY1Njg1N2UyZmExMGFhZDllZDEzYzYyZGYwMzg4ZTM0ZDJmNjkyYzA2ZWJmYWY2NmVkMWJiNTkwZjliNmNmMDRlOGQ2YzUwNzhiYTliYTliZTcwNmU1MjZmNjY4NTIyOTE2M2JjMTk2MzVmZmI2NDJmMjUxZTUyOTMzMTk1M2VlYTdkN2ZkNDNkODQ1YWI4NDgyOTU0ZjA1ZWRhZTBjZDY3NzgxZDc5MzlhOGQ2ZDcyZWJkMGRhZjM2YTgyNjhmYWQyMDVhYjY4YTUyMmQyMTZjMTNhZmNjYjc1MzAyY2Y3MmRkZjk2ZmYyN2Y1ODk5ZWU5MjIyYTYwZGJlNzY3MzlmNDQxZGFkOTM2ZWMyNWQ3ZjczODRlZmZiYzNiYWQxNGNkMmJkZDczMzJkZmZkOTc1OGYyYTdlMTI1Nzk3NGJmZGZiNjU5Y2VjYmEzZDUxNDAyNDRkNmYyZDIyZjA0MTlhNWEzMzg3NDA4MzJjMWI3OWFmMjNhYzRlYTU2OTFlNmI5NjIwMTVmYTI4NDVhMTM4OGZkYWZkNTViNjdjZWVkZWNiMTZhMTA5NzNjYmVmNWQ4MTIzMDc2ODNhODk3MjQwYWM5NDM3ZTNiMTc4OGNlMTVhYTkxZDg3MmY2YzA5Yjc1ZGRkN2EyNmZiOWM0ZjlhNzU5ZTQ4ZGMwMjZiMTI0NDRkMjU5YjNjNDZiOThhNzRiZTk0MDg0NGJhYjRjOTQxMzIyODk5M2IyYWViMmVjMGQxMDhhY2Y2ZGQ2YzAxMzE1NzM2ZTlmMzFlNWY3MWRlZjQ2ZDAzYmNhZjA1YTE2YzRjOWUwZTE5YWM1YWJiZmY2Y2Y2ODQ1MGZmYzQ5Y2U4NDk0OTljOWY3OTBkNDBhNWI3OTczMDA4YTYwZmUyNjkwYjdlZjY2ZDUyNDljN2ViYThmOWEzNGJmMTU3ZTVjY2YxNzQ3OGQ5NTQxMjNkYTFiMjQ0NmI0MmRmM2VmNTNlYWI4NmU2YzczNTdkZWJmZTZiYmQzM2U0NGJiZDg4MTZiZTg1NDlkMTY4YzQ5NzA2MGJmMzgxYzMxZTA0YWNmYzBlYzNkNDI2ZmY2NTIwOThhZTYzY2RkYzVkMDFkZGJmYzNkMGE3OTk0YzZmNDBiMzZjYWU5YWIzYjI0OTIxNWRkM2YwZTI4MzdkY2ZhOTQxMjZmY2QxOWM2NmE0ZjExOTMxYjcyZGZmNmIwMTkyYmY1Mjc3ZWQzZjdjIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA0MTcwOTA4MjMiLCJ0aW1lc3RhbXAiOjE3NTE5NTMxMDM1NTAsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiNDU3ZmQyZWMtODQ2Yi00MDA0LTgzNGQtOWYzYTRlMTM2ZWMyIiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiYzgzZmI5YjEtNjJjOS00OWVkLTkyMDItNDRmZmZlNDM5ZjgxIiwidG9rZW5UeXBlIjoiQUNDRVNTX1RPS0VOIiwibGlmZVRpbWUiOjg2NDAwMDAwMCwiaWF0IjoxNzUxOTUzMTAzLCJleHAiOjE3NTI4MTcxMDN9.C1onhIKnRMXrg2mnKGHdj-yYFB-e_MWHEtgydiYl7mY	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJhODQyYTE1Mzk1MDM3YmE5Y2ZkN2E0YTg0ZGFhZjg5N2UxMDA3NTRiYWZmZDk1ZDFiMjk2MWE3MzdmNGQ0ZjczNDI3NDllMDBkYzkwZTBmNzRiNjVlNzcwNWUzNDljMzY3MzA0YjE5MWI3MDdhYzcxN2YxMTNiNWYwZDU5ODk5ZmU3ZjY4MGE3OTZlOTRmZjZiM2RiZDdjMjFiOTU5OWE2MmQ3Nzk2NmFlYjMxYzMxY2VmZTBhZmYwZTNlOGIxODdlZDcyNmNlODhiNGU0MDBkZDk5YWQ4MzA4MjY5NDNiZWM1OTRhYWNiZTg3MmQyNjk3YmQ5MDI0ZGI2MzYwYzExMmIxOTc5MDM3OWU4OTRkNDgxNjJmYmIzYTA4ZmQ5NDUyZTU4NGY5ODJkMmI4YjEyNWZhOTM3NWI4NGM1MGI2MDQ2MTI2ZGVhNTM0MzkxYmRlZTJiZjg0YjQ3ZTg4OTg1NWNkZjc0OWQ0ZTYyNTBiMGU0NjEzNzZlZmFkZDE1ZTliYjZhNDU1YmUyZWY1ZTFiM2E5N2UyYzJhYTFhMjMwMThiMjcxMTUxOGQwMWQ2YTU3YzdmMzY4YzdlMDRjZTM3NGM1ZThmZjhmYjMyZWNhYjc3ZTFhNDg4ZTJkZDNjMjlmMDdmZTA0MWI3ODJmZGI5NGU5ZWJjY2RiMGI5YmI5ZDE2NjUzNjU2OTc5MDQ2M2E2ZmIwYzNkMTYyODQ4NzYyZTViZDQxZDIwY2E5ZDQzMzRjMWUzNjA0NTEyYzQ1ZmExNDY2OTYyODMzMzg1NjNiMjA2NzJlNjkzMWY4MTQxMTZiZDFhMzViMmY5M2YzNjZhYTA2YjViNGVjOWEzZjBkNTY1MTZhZWFjNDhjYzMwMmQ5MDIxZTlmM2VlY2I2MmVkNGUxMzJjZDNlODg1NDg4NDk1ZmE0YjRkZTdkMzJiZGJjMmI3OGNhNTE1ZmVhOTg1ZjU1NTcxMmE5NGU3ZTkyMmYxM2RmNTRkNzcwMzQwYzQwMDdmODNkNmZiODY3ZWVmOGQwYTExZTY2MGI0M2JhMTAxMmQ1YTM1NWExYWY4YjE4ZmM3YzVlMmMxYzc4Mzc3NmJkODA0MTMwYjQxNTMwYjc1NDVkMWQ0YzE3MjM0ZmI4Mzg3MmFkNjBiZDAwZjM1ODZmNGUwNmFmN2FkMWI4YmZjNDMwMjMxY2VmYjExZTIwZWVjMmNkMzQ1ZTljNmQ4ZDdmM2JkOGQ1MzQ1ZGJkMTgxOWQ4Mzc3YTUwMjQxMTQyMmM3NDZiZmU3YWU3NGIwOWUwZGM1ZGNlOTA1YzYyN2Y0ZjIyYjQzZDM5YmJjZGY2ZDIzYWM2NzFlMjNjMGMxOGUxYTFjOTgyZWY3YmVkNjIxNTYyYThkOTYxNzIwN2VjNmQ4NzA0ZTA5YmI5NTdmNWJlMDRmODE0NWYxMzU0MmFmNTk4ZmVmZWE4ZWU1ZDM2ODVjNTgxY2E5NmQ0ZjgxYTdiMmUyNTI0NTA0MzM2Nzk1NzhlZDg3MWVhMzZlZTgyMzQwMDc3OGZkN2FkZjhhOTVkOGQ0ZjNlNmU0MDgwNzY2Y2RhM2Y3MzA2ZWVlODBjNmM4ZjRiYmY0OGVjNGE2ZTJlMzJiM2E4MWYzYTlkMTYzYjdlNTBiZDMwZmNmNDQ5YjUxMWE3MzI5YzU3M2U4ZGFkNjdjNTQwNTdiODE1NjBjMDg3MTJlZDhhMTE5NzdmYzJiOWY2MjNlMmY4ZWRhYjRjZTQ3Y2NjOTE0MWU5YjRmYTNjZGFkMGZhMWUxNGFmYmJjYzViYzA2ZTAzMDI2ZmU2Y2NkNDUxMGQ3ZjA2MDBkODQyN2RjNTc2NzAyOTNhZDI2MTYyYTMzYmMzMGE0OGUzMWRhZjUyMDNlYWUzODc4YmM1ZWRkNzEzYzI2ZjdkOTQ4YmE4OGE4NjMzMWRmOTc4NzM1MWU0MWQ2NjMwOTc4ZTM4MDBlYzRmMTUzN2EyZjViNmRiOTAwNTgyM2I4OWY3ZjRlMjA0ODAwMWUzMjQ1NDMxNzUxYmU1NGYxOTlmNDhlZjRmMDc4MDM4ODlhMTZjZjhiNTFiYzk3OWE4NjAyNGQyMmEyNzYzNGI4MjU0MWM0NmU4MTFiMGU5NDY0NGU5M2JhODhjZjcxOTNmMDA0M2Q5MzBjNGZjNTQ1ZGZjZTM4NzhmYTM5YzllYzhkNzUwN2EzN2M0MDQ1MjczYjg4NmFkODg5MGI5OTdiYmQzNjc4YTY4YjEyZjM2ODM4OTI3MTNjOGUyMDQ3NDUwMTMwZmYyMGZjODVlOWY1MzZiYzllYWQ4MDhkYzBlZmE2OTVlMzBlZDY0MzQ4ZmQ1MzY0OTYxNjhkM2U5MTkzMGZiYjFhYjAzZTdmNzZhNzhkODZlYTY3Y2VkMTQyYTc4MTRkNDc5OGQ3ZmQ2YmZlYTY1N2UyNDU0ZDAwN2E4YmY0ODM0MGU3YmY3YjkxOTBmM2VmOGY5YjBlM2E3MGM4ZTQwYTMzOGZlYTRkODkzODc5NjQzNTk1NGUwOWUwMzBiNjI5MzVjYmZkODVjNGU1MTliMWIzNWYwYzBmNzAyM2I1MzcyOGRkNzgzOGJjYTM5NzkwZjcyMmFlNGJmMzA1MzRjZTllMTY3MmM0ZDJmNmY3Yjc0OTg3ZDM5ZGZjOWVlYTJhMzFmM2UxZmNhMGE3NzIwOGVhNjk1NzMyOTk1NzZkYzNiZDA0ZWJmZDM4M2I0NGJhNTY3YWUwZWI3NzJhYTBjNDNmYjM3OGU0YTYwYzY3NGEzMTM0NTk2YTNkMjEwODJmM2E5ODQ4MGJiMmFmMjIwZDg3ZjhhNmVkNmNjMjZhOGY1ODAyMGNlZDUzNTZiYjQxMWJhODE3ZDQ3YWZlY2Q1ZTBlMWM4NTZhNDVmYmU4ZDA2OWI4NjM1ZTNiODZiYzBmNjA0ZjJkOTM4MTAzZTRjNTE3OTQxODk1YjYzNDAwYWU1NjI5OGNhOGNjM2VhNWM1MzZhNWEyYzE3YjBkMjVkZTBmZjViNjQwYTM4NzZmNWVlNjcwYTRhNThjM2JjZjUzNjhjOTFjODJmZWFkNGM2ZmY2MjYyYjg2ODJiZWY0ZDg2MzQ0ZGQzNDkyMWY1NDYzYmZhNTA2NzhiNjdiMGRhZjdmNDI1MmVmZWQwMTc2MDJmN2YyYjk2ZDhjODM0NzI4YzFlZDRjMTAyNmMxNzIyYmQ1NzU3ZTFiZTgzMTU3Y2FiZDM0NDdmYmE2YTk0NjQzNWJjYzk1YmUwMjVjOGQ2ZmZmMzYxZWVkMTAzNzEwMDU1NGI2ZWNmYzBkM2YyYjZhYWU2ZmNmZjVjNDM0ODNiMjVkNjU1OWQ2NmM0MTVhNmUyNmYzNWY5OTcyMDgxYTA1ZWQxNjljYjk2YjEyOGYwNDdiY2RjMTljMWJhMDM5NTcyMjkwNjRlODMyMjdkZWRhNTgyNTY3ZGY1YmUyNThmZmVlY2UzYTgzOGYzNTBkY2FhYmVhZjBiNjNiZWU0NzE1ZTcwOGI4MTExNDA3YTJmM2NkNjAyNWZlMjBmZjgxNzM1ZDAxOGQxZTIzOGJjMDFmYWRmMDkxZmU3NTMzMDE5ZjMyMjBhY2ViMjMwMmFhMzVhZGUzNmI0M2U1ZjgwMGNhMDYwMTdhMTg0YjBiN2NkYWU3N2FjMzU3MGQzNTRiMzQzOTY2ZDBmNjRjY2MyZDljZTdjZGNiMGYzYjYzMGUzYTA4NGY0ZDViNTFhNTA2NGE1MTYzYjZmYzczMzU4YzYyN2VkM2M0N2M5YWYxNTRiMWE0MjAyNDhiM2U4NzJjYzA3NmExNWJlODVlY2VlNmRkMmQzNGM1MzNkNTE2OGIzNmI1YTAwNTFlOWQ1Zjg5NjBjMzRmZjFlODFlYWI4MGM1MGRhNjYxNGZjNGJlNjc5OGNhNzg2ZWRjNGFmZDA5ZmVkNTU5Y2IxNDE5MzBiOGZiN2Q5NjNhODJjYjBiODcwY2E4ZWYzMGQxYmY2MmY2ZGJmY2RhNDZlZTYwYzU1ZGNlNDZhNGRjMTVlMDcxOTk4ZThlNGJkNDhjNGY0YTUzMWJlN2E0ZGUxZWJkZTFlMzIyNzJiMWI3Njg4OWNkMDFhYTEzNDFmMDI0ZWYxNDhmYjhlNzM1NTU2Nzg0MjEzOTFkOTM4MzEwZTJkMWE0MjI3MjhkZDAxNTc2MmVlYjRmNmFlNDRkYWJmYzJmMTlkYTQ5YjMxYjlkNjljZTgzYTkwYzcyNjc3MzQ0OTgxYzRjMzA3ZTQyZmQ1ZjJhNDVhOTVhZjg4NjIyODM3NDMxNjAyNmIzNTlmMTBmMDEwNGE2MzgwYzcyZjBhY2Y3ZGIzYzE1NmI5YTM0MzNmYWVlYmYzNzQxMThmZDYwOGVmNmJmMDY0OTk2YWI2MWRkMzAyOGExZDI5ZWJjMjQ4YTg0MzZmZGQ0NzUzZjBjMWM4OWNjYWJlN2NjMTZmNzA1ZWI0NGQ2YWNjYTU4MTA2N2Y3OWE1NWVkN2FhYmZjN2MyY2Y0M2I5ODliOTQ5MzNlNmEwNzFkZmEzNDU5MDcwMzI5MjJlZjZkZmM1ZGI5NDI5MzU1YTMyMTE0OTJjNGYyN2I5NDNhY2IwMDI5Nzc2NTM2Y2JlNjc3OGQ4MDcwMWZhYzQxNmE4MjZjM2Y5ODU5OTQyYjRmNWJmOGJlYjNmODVkNWY2YzgzNWJkMjcwNTRhYTYyM2EzZmUyMzRjNDM2OTU0ZGIzZGQ0YjUzMjk4ZjQ4MmVmOWRmN2I2YzI1NTNhZDc3Y2MwNzc1MjZlYzE4MjUzZGM4ZTM5YTQ5OWRlMzY3MWFlYTM2NTI4NTdhYmQ0NWYxOTdlNjdhMWEyYmUxOTYwNTA3ZTk0N2I1Mzk1MGYwYTE1MWNjMzQxODMyMDhkYmUxZDM1MjNiZGM4OGZiMzk1Y2M0YTkxNzQxNWFmOTAyMjU2NTFlNmMxMGNmZTdjMTM2Y2RkNGNmYjE1MjM4NTk2M2VkZWQ1ZWIyZWUxNTAwOGMwNjUzZTlkY2VjZTFiZjMzZDQ2OWY0NjEwYzJkMjE3MjEwMjM1MjljYzYxYzkyZTA4OTEyMjViY2VkN2NhOGVlNTQ3YzcyMjgwMGI2NWMzYzkyNTgwOTY2Zjk1ZWM0MDI1MzIxMmMxMzhlMzI4ODc1ZTA0NDgzOTIyNDg1NTg4N2EzOTk3MDM3OGMxZTU0NDRhZWVmODg3ZjUyYTcxMzY5YjRjMTljMjE4Y2U4MWRhZDBkYjM2YWI4ZDc3MDI5YjQ1MzBkMGJiY2U1MDg5NGFkMWI2OGVhZjk2MzYzMTNhYzRhZjk3MzY3NzM3MjA2MWQ4MDgyZjc1MTIxNWFiYTY5NjEwYTdiNDQ2ZTk5MmRmOGE2YmZjMDYxM2JkMWE4NTM2OWM4MGNlZTQ1OTY2ODQwNjc5Zjk2NjM0ODRiOTRhZWVhMDYxYTA1NzY0OGRhNWYxZGY2NTZhMTQyNjRiNzQ0MWIyMmUzZTFmOTM3NmEyMGY4Y2IxNDVhNDBkZjcxMTdiMTM3YTMzNGY0NTEzMDJhMzVmMzEzYWNjZTM0ODk4ZGIwYjRkZTY2Nzc1OWYyMTFhYzU4ZDE1M2QzN2FiYzJmZGMwNzY1YjE2ODQzNjE1OTc0NzRlZGUwMmE3NGEwMjU2NTJjMDk2MGI3MWY2YTliN2VjNjc0OTY2OWJlODUxOWM2OGE0MDcyMTY4MjU5NGU4YjdiNDllM2E1MWY4MmNiZmM3NTM0ZGZmYTNmNGMyNjk0ZmNmODExM2Q5NzFlNDBkYTA2YTY1NWEyYjgwM2E0N2U1MzJhMzQxOTQ2Mzg1YmY4N2JlMjllNWVhNjkxMWUwMmQzZjE5YWExYWI5NWE4MDhiY2UyNGRkMWFlYTRiYTVlODBhNTEyYTUwZjEyMGZjM2ViODhmMzhiNjE3NWVhMTRlMTE4MzE3OTFhYTI0MzAwYThlYjkzYTIzNTBiOGI2MTFhMDMzNTk4MjhjMjY4ZDA4MDU4OTAzMDk5NzNjNTcyNjk4ZWYzMmVhYjJiNjUzYThhZDA3MjJkNDI0MWY5NWQ4ZTQ2MWY1ZGE0ZTMwMTI1NTFjNWY0YzM2N2Y3ZmE3YjcyZDg2NjhmZjMwODY0MzFmZTMxZGQ3OGJmMTJjNTM5YWM4NDI5YTcxNDE0ZDk5YTYxNDFjYTU5ZDVhZDc2NjFlOTRlZDZjNjkwNzRiZWU1M2E0Y2VhZjk3ZGMxNTY2YzA0NjY2NjUwODA0YjQyNzY5NDIzZmI5YTY0MjM4NmU5MjMwYzU1YjdmNWU1ZGQ5YThmMzZlMzM5YzgzNTdhMzA0ZDUzMzk5ZjQ2OTk2YmU3ZDI5N2I3MmIxMTEzMmIxZTVkOTBhZjEyODY5ZDI0ZmIyNjAzODBlMTQxM2VjYzUzNTQwMmRhY2VkOGE2MjMyMDVhOGRlODQ5MzI5ZjlkODllNTU0MjM1ZmE5NjBjYTBhNjEyZmM1MGRjNzM3NzFmNGVjN2M0OGJiZDNmMTA0ZjNiNjg4MzE3MWJmOTJhZTIwOWFmOTcxMTgxZGZlOTA4MzdhODY1MDkwZDk2MGNmZjQ5ODI2YTMwY2VjNzYyMjM2YmY3ZjcyZWMyOTFkNmRlOThjZGQxODIxZjc2NTI0ZDQ2ODYzOTRkYTg1ZmJiZjViZDk3MDEzZjY4MzQyMWU3MTNmOGM4MzFlNDk1NWVlN2U5YWY4ZTkwZTNhZDA4YzY2ODc4MGFjNGQ0NGIzMGNiNWRlMTI2YWM3MjRkZDRjOTc5ZWVlM2Y2ZmFiYjEyODc3ZDdlNWQ1ZmM1OWRjMDEzZTIyM2FkNzRmOTcyY2EzMmNjNjMxZmI3NDczNGJkYTA2NGE4OWUxODBhM2ViZDhkMjMyYmI2Yzg4NTAxYjE2MDliNmUyZjUxZmUwNjIxOTkwZGRlZDE1NWQ1Y2ExOWY3NzBhZjM1NjAyM2M1MDg3ZGEwN2I4YmZlYmYyOGMzNTBlMjBkZDI3OTQyM2JiYjViNjg0ZTg5YmQxNTkyODkxYWVkM2MxYTcxNjI1Nzg5NmFjOTA5MDhkNDYyN2Y3ZTIyNjg3N2M0NTgzMmEyNmI0NjY5ZDA3NDNkMTQyYTBjNWUyYTY2NTcxMTE3YjBlZjU3Yzk4ZDFmNGNkN2I5M2U3YjQyMmQ4NDhmZGRlNzBjMDAxNGVkMDRkYjE1ZTljMWRjOGNmYjA4YTEyNmFjYzA0NDBjOTg2M2E1ZmE3NzJkNzBhZTgwNjQwZTQ1MTI3ZmEyZTQ2Y2Q4NWMxMTQ2NWNmZDI4M2NkMjU4MDcxN2Q3ZTdjMDhmMzFjNDdjYzY3YzBjZjRkNTMxYjlhYmU3OTgwZjY5NTAzODEwYTJkODM2NmFlMjhlMmFhZWJiYzBlYzgyYzhlYTU4MDU2YmFiNDBjMTlhYTYxMWRiNTE1MGE4ODM0ZjBiNDU5Y2FjYjNlYTUwYzc4YmRlMjQwMzBkYjFlMWI2MWUzODhmNjk5ZDkzMDU1NmU1OGZjMDQ5NDI0MTQ3MmY1N2VkYjc1ZGE0NWY0OTFhYWNjZTdhN2Q5OWNmMGQzN2IyNjNjZjA3ZTJjNWM1OTJlN2ZhYmFkNDBjN2U3NmZmNjc2ODM2NGNkNDc4NGZkYzg1YzZhYzkwZDQzMzM3N2E3ZTYzMjU4MzA0NWUxZTgxYjZhN2I4NjVkZWUxYjlkZGEwYWUzMzI1MDg1ZjNkYTcwNTc2ODQzZmY3MDg4MzExZjM5OGM4M2NkMDFiMTQ0OWU0NzUwZThkYzU4NDc4NjdiYzk3M2FlOTYzY2IyZmUzYTczMTEyYjcyYjI3OTI0NGQ2Zjg0ZDJhYzczYWE1NTRlNmE3YWExMTE1YzI5OTY0ZDFlODY2ZjQyOGVlNjM4NzMwMWIyZGQyZTdkYzI0NGNlOTBkMjFmNTIwM2IwNjFiMGMwOGQzZGNmZDI3ZDkwMTcyZDcxNTQxZmQ5ZThmODY2MzU3MGM5NzQyZWZjYjU4N2ZkNWI0ZDJiYzQ5ZWQ1ODc4ZjBmOWY5MDNhYTMyMDBjNGU1ZTFmMWI0OTQzMDUzN2RkZTQ2OWU3OWRjODllNGIwZDc2ZTdkOWViNWUzMDgyMGM1ZWE4NGJmMjM3N2ZhZjM0NDA3OWFkMDIwOWEyNGFlYjAwNjRmYzBhOTI5Y2QwYmY5NTNmMjE5MjU0OGNiZWRmZDM5YTk2MWY2ZmVjZTYxODFmZDQzNDQ1NTFlN2E5ZjJjOTc5ODA2NjI3YWE4MjYwYWMyZmRlMzY1YWNkYWNkMzg4MDhkZGE0ZTVkYjA2ZGRkYjJhOGQwYzcyOWFjNGU4NzU4NDE5ZTFmODAxZTI1MjgwOGFkOWI3ZGVjOWQwYzA0N2NhZGQ4NjBhNTg3MGViNGM4YWEwNGYwNWUxZjEwM2E2MjIwOTI1ODMzZjQ3MDQ0ZWZiMmYyYzc2YjMzNDI0YjY3YzAyNDZmNjFiZjVhNjliN2JhZDYyMjljMjczMTVhODgyZTM4ZTE1ZDMzZGU4ODZjMjMwMjRkN2VlYzY5M2Q4ZmU0Njk4MTc1OWExMjk0NDQ5MWM5MDk3Y2IxMzdkNzcyMWJjMzBiYjc5OTEwNzVkNjUxNGIyYzU3ZjRlNmVhZGQwNzE1YTMxMzAzMGVhMGEwNWM2NGUyODZkM2I2NGU2NDFmYTZkNGJlYTYyMTNmMzVmOGQ1OTc1ZmVmNDU5MjMyNDU3MGExYjFhY2JkMjcyYjFjY2JjNjZiZWI2ZWUzMTgwZDY5NjA0NDFhMzM5MDllZjBhNzE5YWViOTQ2ZGJlZTNhMWQzYTkxMDY5MmJmM2M1ZDg5Nzk5YmE3ZmE2OTcxNDA0Y2JmYTE2MTM2ZjkyYjRlYzE0OTc2MTNkM2Y1Y2VjMjIxMjdlMTdhMWNmNTFmOGZlOGUxMDNlNjk0YzI5MjVkZmI5YzA0MDM1ZjcyYzI0NWY1Njg1N2UyZmExMGFhZDllZDEzYzYyZGYwMzg4ZTM0ZDJmNjkyYzA2ZWJmYWY2NmVkMWJiNTkwZjliNmNmMDRlOGQ2YzUwNzhiYTliYTliZTcwNmU1MjZmNjY4NTIyOTE2M2JjMTk2MzVmZmI2NDJmMjUxZTUyOTMzMTk1M2VlYTdkN2ZkNDNkODQ1YWI4NDgyOTU0ZjA1ZWRhZTBjZDY3NzgxZDc5MzlhOGQ2ZDcyZWJkMGRhZjM2YTgyNjhmYWQyMDVhYjY4YTUyMmQyMTZjMTNhZmNjYjc1MzAyY2Y3MmRkZjk2ZmYyN2Y1ODk5ZWU5MjIyYTYwZGJlNzY3MzlmNDQxZGFkOTM2ZWMyNWQ3ZjczODRlZmZiYzNiYWQxNGNkMmJkZDczMzJkZmZkOTc1OGYyYTdlMTI1Nzk3NGJmZGZiNjU5Y2VjYmEzZDUxNDAyNDRkNmYyZDIyZjA0MTlhNWEzMzg3NDA4MzJjMWI3OWFmMjNhYzRlYTU2OTFlNmI5NjIwMTVmYTI4NDVhMTM4OGZkYWZkNTViNjdjZWVkZWNiMTZhMTA5NzNjYmVmNWQ4MTIzMDc2ODNhODk3MjQwYWM5NDM3ZTNiMTc4OGNlMTVhYTkxZDg3MmY2YzA5Yjc1ZGRkN2EyNmZiOWM0ZjlhNzU5ZTQ4ZGMwMjZiMTI0NDRkMjU5YjNjNDZiOThhNzRiZTk0MDg0NGJhYjRjOTQxMzIyODk5M2IyYWViMmVjMGQxMDhhY2Y2ZGQ2YzAxMzE1NzM2ZTlmMzFlNWY3MWRlZjQ2ZDAzYmNhZjA1YTE2YzRjOWUwZTE5YWM1YWJiZmY2Y2Y2ODQ1MGZmYzQ5Y2U4NDk0OTljOWY3OTBkNDBhNWI3OTczMDA4YTYwZmUyNjkwYjdlZjY2ZDUyNDljN2ViYThmOWEzNGJmMTU3ZTVjY2YxNzQ3OGQ5NTQxMjNkYTFiMjQ0NmI0MmRmM2VmNTNlYWI4NmU2YzczNTdkZWJmZTZiYmQzM2U0NGJiZDg4MTZiZTg1NDlkMTY4YzQ5NzA2MGJmMzgxYzMxZTA0YWNmYzBlYzNkNDI2ZmY2NTIwOThhZTYzY2RkYzVkMDFkZGJmYzNkMGE3OTk0YzZmNDBiMzZjYWU5YWIzYjI0OTIxNWRkM2YwZTI4MzdkY2ZhOTQxMjZmY2QxOWM2NmE0ZjExOTMxYjcyZGZmNmIwMTkyYmY1Mjc3ZWQzZjdjIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA0MTcwOTA4MjMiLCJ0aW1lc3RhbXAiOjE3NTE5NTMxMDM1NTAsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiNDU3ZmQyZWMtODQ2Yi00MDA0LTgzNGQtOWYzYTRlMTM2ZWMyIiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiNDU3ZmQyZWMtODQ2Yi00MDA0LTgzNGQtOWYzYTRlMTM2ZWMyIiwidG9rZW5UeXBlIjoiUkVGUkVTSF9UT0tFTiIsImxpZmVUaW1lIjoxNTU1MDAwMDAwMCwiaWF0IjoxNzUxOTUzMTAzLCJleHAiOjE3Njc1MDMxMDN9.fj_O7cp5rojHpNncoaoYLPwlcZoh7k1uQ2bzp0mVHfo	2025-07-17 08:30:44	2025-07-17 08:30:44	2025-07-15 13:54:58	[]	t	2025-07-01 08:30:44	2025-07-15 13:54:58
4	fino_insurance	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJjOGIzMjVhMmY1NDI5YjY4YWFlNjc1N2NkYzhiODljMzI1ZjQwMTZhOWYxODQ1ZDBhM2M3ZGViMmFmM2NhZWM2NzI1NWZhOTFlOGU1NjBmMzFmYTQyNjMxY2I4NDBkMjZkN2Y1ODVkMDY3YjdlZGEwMGZmMThiMGE3OTBjODg5YWE3YzcyMGI2YzIyZGJiODNlM2ZiOTMzMjhiZjQ0OTYzZjk5N2Y2MGFhZjA1ODM2ZDNmYTBkZjcxMjI0ZDQ0NTdjYzY3MGMwODZmNWVjMWNjYWQ2YjY4YzAwMmE4MzZjZTQ1ZTUwYjZiNTkwM2UzMTgyZTlkOTZhOTA2YjZkZGIwYmFhODI4NzZlOThkMjRmNDM0ODJmZjYzYjRlZjM5ZDVlYWM4OWZmZDFjM2I4Yjc3NGZiYzM3M2FhNGM0N2JjMGI2MjM3OWFhMzIyMmUxOGRlZTZmMmRiZmY2MWNiOWMwYWNkYmQwYzg0ZWQxNDAxNDY1NjBmMmJlOWIwY2I1ZmM0YTNhNTFiYjI2MWZkZTFmOGJiNzIyYTcxYjdlNzMzMGRmMzQ1NTU1M2QxMTczNTQ2ZWZlNTJlZDBiNjEzZjcyZjkzYmJhNDgxZTM3ZjhjYjcyZiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NjA4IiwidGltZXN0YW1wIjoxNzU3MzM3OTY4NTM0LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImUyOTIxZWMzLTQwM2EtNGM3NS04MGQ4LTdiZDcwZTBmZjAzYyIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6IjUyMTc2MjZmLWJlZDItNGQwMi1hZjc0LTZhNDczNGM3OWQ0NSIsInRva2VuVHlwZSI6IkFDQ0VTU19UT0tFTiIsImxpZmVUaW1lIjo4NjQwMDAwMDAsImlhdCI6MTc1NzMzNzk2OCwiZXhwIjoxNzU4MjAxOTY4fQ.qyLIE8v51RoMbvzDoTvsJndcasl10F3HwAik5_ZWKkM	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJjOGIzMjVhMmY1NDI5YjY4YWFlNjc1N2NkYzhiODljMzI1ZjQwMTZhOWYxODQ1ZDBhM2M3ZGViMmFmM2NhZWM2NzI1NWZhOTFlOGU1NjBmMzFmYTQyNjMxY2I4NDBkMjZkN2Y1ODVkMDY3YjdlZGEwMGZmMThiMGE3OTBjODg5YWE3YzcyMGI2YzIyZGJiODNlM2ZiOTMzMjhiZjQ0OTYzZjk5N2Y2MGFhZjA1ODM2ZDNmYTBkZjcxMjI0ZDQ0NTdjYzY3MGMwODZmNWVjMWNjYWQ2YjY4YzAwMmE4MzZjZTQ1ZTUwYjZiNTkwM2UzMTgyZTlkOTZhOTA2YjZkZGIwYmFhODI4NzZlOThkMjRmNDM0ODJmZjYzYjRlZjM5ZDVlYWM4OWZmZDFjM2I4Yjc3NGZiYzM3M2FhNGM0N2JjMGI2MjM3OWFhMzIyMmUxOGRlZTZmMmRiZmY2MWNiOWMwYWNkYmQwYzg0ZWQxNDAxNDY1NjBmMmJlOWIwY2I1ZmM0YTNhNTFiYjI2MWZkZTFmOGJiNzIyYTcxYjdlNzMzMGRmMzQ1NTU1M2QxMTczNTQ2ZWZlNTJlZDBiNjEzZjcyZjkzYmJhNDgxZTM3ZjhjYjcyZiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NjA4IiwidGltZXN0YW1wIjoxNzU3MzM3OTY4NTM0LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImUyOTIxZWMzLTQwM2EtNGM3NS04MGQ4LTdiZDcwZTBmZjAzYyIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6ImUyOTIxZWMzLTQwM2EtNGM3NS04MGQ4LTdiZDcwZTBmZjAzYyIsInRva2VuVHlwZSI6IlJFRlJFU0hfVE9LRU4iLCJsaWZlVGltZSI6MTU1NTAwMDAwMDAsImlhdCI6MTc1NzMzNzk2OCwiZXhwIjoxNzcyODg3OTY4fQ.jZozhFROzt1H9l1U1zHu9w4aJ0WiwCUM4T68Oiuzc7I	2025-09-18 17:26:19	2025-09-18 17:26:22	\N	[]	t	2025-09-08 16:56:26	2025-09-08 16:56:26
5	fino_vehicle	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTI3OGMyZDQzMzQ1YjIwZWY5YmU2NmY1ZmQ4ZDRhOWQwMzM1YTQzNWViNWU3ODYxOTEwMjU2ZWVkM2JhMGMyZTcyOTc3MWNmODA4YzUxNjQxMzFmYTU2MmMwZGJhMDNkZjZkMjI1YTQwMWI2NDdlODQ0OWZiMTJiNmUwODY4ZDg1YjAyZDJiNWI2ZjI1ZDRiMTZkMzJiMTdjMjJiOTVmOTk3Yjk0NzcyOWEwYmI1NzNkY2VmYzFhYmIxNTI5YzAwYTcyZDE3ZGQ4Y2RiMmYxNTA5NTgxYTY4NDAyMjU4ZjM4ZWExNzUxYWFiNGQ0MjQyNGNlZjBkYTYwZDg2YzcwY2Q1M2IxOGQ5ODc4OTQ5YTQ2MTY1YzNlYTAzNTQwYmE5MjU4ZmU4NGVlZDRkMGU3YjE3NWI3ZDc3Y2IwNDQ0N2ExMGE3ZjJlZGViODM1NjUwYWQyZTBhOWNkYmE2ZmM2ZGQxYzliYTMwMDllZTAyNzBjNTExODFiNjdmNGJkZDA0M2Q4YTVlNjBlYjIyNGZkZWNlNWFiMzYzNzdmZTNlNTI0MGFiOTcxMDk0YmMzMDEyNDE2OGFhMzdhOGZmYzRhYWQ3MWMyYjNmMGQ2YTAyMmM4ZjA3ZjQ4NDI4ZTNhZGY4Mzk4MDZlYTVjMTk3ODMzOTQ4M2ExZmM4Zjg1NGRjMGE4ZDYyMjU5NmI2ZDJkNDg3NmExZTExYjczNWYyNzQ3NzkyYjVkOTUxZDNhYzJkMjRkMzRkN2FjNzcwZDA1ODcwMWU3MGU3ZTY0ZGEyM2RkNjRhZTU5NzBmNDg2NDQ4MDE3MWZiMDE1NzFiN2ZlM2EzYTM5YjE3MTRjNGRjOWE3YmQ4YTJlMDlmYWE2MTBjZTNkM2E5YjJkZmViNGFjOWE2MWExNGUxMjI1ZDlmN2QxMWI4ZTgxZmY0ZDRjZjc5ZDc2ZGRkZmE1YzFmYjVhZTliODg0ZjM1ODI3MjhjYmUzYTc2ZmE3M2ViOTQwNmIwMjRiZDUwYjY4OGFkY2ViOWQ2NGYzODAxZDFmZWQ3MmY5NWNlYTQ2MmMwYjdjMGEzY2ZkZjg4Zjk2OWRiYTliOTJkMzZmMzY4MTQxNTQ0MDRlIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA2MTcxNjU2NDgiLCJ0aW1lc3RhbXAiOjE3NTczMzgwMDg0NjQsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiMGEyNDQ0MzItYjkyYS00YjExLWFkNTItMGIwZjlhNzFkZjFiIiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiN2I1ZTgyMzQtOGYzYy00OTYzLWE3MzUtNzlmNmM1OWJmNThhIiwidG9rZW5UeXBlIjoiQUNDRVNTX1RPS0VOIiwibGlmZVRpbWUiOjg2NDAwMDAwMCwiaWF0IjoxNzU3MzM4MDA4LCJleHAiOjE3NTgyMDIwMDh9.f3Ryry133wy3BFLXSfv2m2BgU8f2rFHfyEeG-XUxNBE	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTI3OGMyZDQzMzQ1YjIwZWY5YmU2NmY1ZmQ4ZDRhOWQwMzM1YTQzNWViNWU3ODYxOTEwMjU2ZWVkM2JhMGMyZTcyOTc3MWNmODA4YzUxNjQxMzFmYTU2MmMwZGJhMDNkZjZkMjI1YTQwMWI2NDdlODQ0OWZiMTJiNmUwODY4ZDg1YjAyZDJiNWI2ZjI1ZDRiMTZkMzJiMTdjMjJiOTVmOTk3Yjk0NzcyOWEwYmI1NzNkY2VmYzFhYmIxNTI5YzAwYTcyZDE3ZGQ4Y2RiMmYxNTA5NTgxYTY4NDAyMjU4ZjM4ZWExNzUxYWFiNGQ0MjQyNGNlZjBkYTYwZDg2YzcwY2Q1M2IxOGQ5ODc4OTQ5YTQ2MTY1YzNlYTAzNTQwYmE5MjU4ZmU4NGVlZDRkMGU3YjE3NWI3ZDc3Y2IwNDQ0N2ExMGE3ZjJlZGViODM1NjUwYWQyZTBhOWNkYmE2ZmM2ZGQxYzliYTMwMDllZTAyNzBjNTExODFiNjdmNGJkZDA0M2Q4YTVlNjBlYjIyNGZkZWNlNWFiMzYzNzdmZTNlNTI0MGFiOTcxMDk0YmMzMDEyNDE2OGFhMzdhOGZmYzRhYWQ3MWMyYjNmMGQ2YTAyMmM4ZjA3ZjQ4NDI4ZTNhZGY4Mzk4MDZlYTVjMTk3ODMzOTQ4M2ExZmM4Zjg1NGRjMGE4ZDYyMjU5NmI2ZDJkNDg3NmExZTExYjczNWYyNzQ3NzkyYjVkOTUxZDNhYzJkMjRkMzRkN2FjNzcwZDA1ODcwMWU3MGU3ZTY0ZGEyM2RkNjRhZTU5NzBmNDg2NDQ4MDE3MWZiMDE1NzFiN2ZlM2EzYTM5YjE3MTRjNGRjOWE3YmQ4YTJlMDlmYWE2MTBjZTNkM2E5YjJkZmViNGFjOWE2MWExNGUxMjI1ZDlmN2QxMWI4ZTgxZmY0ZDRjZjc5ZDc2ZGRkZmE1YzFmYjVhZTliODg0ZjM1ODI3MjhjYmUzYTc2ZmE3M2ViOTQwNmIwMjRiZDUwYjY4OGFkY2ViOWQ2NGYzODAxZDFmZWQ3MmY5NWNlYTQ2MmMwYjdjMGEzY2ZkZjg4Zjk2OWRiYTliOTJkMzZmMzY4MTQxNTQ0MDRlIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA2MTcxNjU2NDgiLCJ0aW1lc3RhbXAiOjE3NTczMzgwMDg0NjUsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiMGEyNDQ0MzItYjkyYS00YjExLWFkNTItMGIwZjlhNzFkZjFiIiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiMGEyNDQ0MzItYjkyYS00YjExLWFkNTItMGIwZjlhNzFkZjFiIiwidG9rZW5UeXBlIjoiUkVGUkVTSF9UT0tFTiIsImxpZmVUaW1lIjoxNTU1MDAwMDAwMCwiaWF0IjoxNzU3MzM4MDA4LCJleHAiOjE3NzI4ODgwMDh9.DmrNlgvI3bcRgpjqSvf4-8-n5ILYpBHLW0llFkhTkJs	2025-09-18 17:27:05	2025-09-18 17:27:07	\N	[]	t	2025-09-08 16:57:12	2025-09-08 16:57:12
6	fino_promissory	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTM1OTcyZTQyMzQ0NjM0YmE5ZGVkMzQ1ZmQ3ZDNhYjkxMzU0NjQwMDFhYmZiOTA0MDViNjQyZGZhMmNmNWM4ZWIyZTI2NWZhODEzOTYxZDU4MzllNjUwNzgxYWJjNGI5YzdhMjA0YTQxMTc2MTZmOTM0YWY3MGViMGE3OTBjODkwYjY2MDZhMTA3ZTNmZDdhZjdkNjZhNjMzMjRhNzVmOWM3YWQ3NjI2MWJlYTg1NDJiZDRiNTBmZjkwZjMzOTM1MzM5ZDM3NjhkYzdiZWY2MDFkNmQxYmRkNzA3Mjc5NzIzZTQ1ZjVhZjliN2Q4M2U2MWNmYmRjYzc0ZDI2ZjZhZGIwZGI3OTE5MDM3OGQ5ZDVkNDA1MjI4YmMzYTFlZmQ5NTQ4ZjM5ZWY5OTVkY2IzYjQ3MmJmYzM2M2UzNDAwZmExNDczZjYwODBiODNjN2E0NWNiZTBiZjlhYjQ3OWMxOTA0MmMwZjgxNjlmZWUyYTRkNDY0NzA3NzNlNWFkY2MxMGM2YWRiODBjZjU2MGI2ZmNlMGE5NzU2MjdiZjllMTIyMGNhMjM1MTAxZmQ1MTEzOTQ2ZGZiYjZiOGZlMDAxZTIzZmQ0ZTBmNmRiZWUyZmQ3ZjIzYjRjNGY4MzYzZDljYjg0MDdlZTU4MTU2OTIxZGE4MWZlZjhjMmRhMWI5NGUxOWMzYjQ4Njg2NTdlNGQ2MGE2ZmEwMTI1NWM2MzQwNzYyMTViYzIxMTc5ZGFjOTU1MzNjN2U0Njk1MjE2YzA1OWU3NDczZjdkZGMzNThhNmJhZjRmN2FmMTllMDdkYjA2MDJiNTFmMjVmM2YxMjUyYjdkYjU2OTFhNDNjOGIyZWJjMzIxMDFlNmI3NWVjNDM4N2U5NDI1ZTliNGY4OTk2YmZmNTk1ODFkOTZiZTg5MDE4NTgyYzM0MDFjYjQ4NTM2OGI5ZWU1ZDFlMjRjYThlOWRlYjg0NiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NzMxIiwidGltZXN0YW1wIjoxNzU3MzM4MDUxNDE3LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6IjQxOWY2Mzc5LWU4NTEtNDI0YS1iOTkwLWMyNmE0M2I2NWU5MiIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6IjNmNTZjNGUwLTkwNTAtNDk0YS04MzIyLTY1ODExMTMxYTM2MCIsInRva2VuVHlwZSI6IkFDQ0VTU19UT0tFTiIsImxpZmVUaW1lIjo4NjQwMDAwMDAsImlhdCI6MTc1NzMzODA1MSwiZXhwIjoxNzU4MjAyMDUxfQ.g7b2IiKlktvPCiHc_vR11OVVhTA2_3-glLLQOZpUni0	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTM1OTcyZTQyMzQ0NjM0YmE5ZGVkMzQ1ZmQ3ZDNhYjkxMzU0NjQwMDFhYmZiOTA0MDViNjQyZGZhMmNmNWM4ZWIyZTI2NWZhODEzOTYxZDU4MzllNjUwNzgxYWJjNGI5YzdhMjA0YTQxMTc2MTZmOTM0YWY3MGViMGE3OTBjODkwYjY2MDZhMTA3ZTNmZDdhZjdkNjZhNjMzMjRhNzVmOWM3YWQ3NjI2MWJlYTg1NDJiZDRiNTBmZjkwZjMzOTM1MzM5ZDM3NjhkYzdiZWY2MDFkNmQxYmRkNzA3Mjc5NzIzZTQ1ZjVhZjliN2Q4M2U2MWNmYmRjYzc0ZDI2ZjZhZGIwZGI3OTE5MDM3OGQ5ZDVkNDA1MjI4YmMzYTFlZmQ5NTQ4ZjM5ZWY5OTVkY2IzYjQ3MmJmYzM2M2UzNDAwZmExNDczZjYwODBiODNjN2E0NWNiZTBiZjlhYjQ3OWMxOTA0MmMwZjgxNjlmZWUyYTRkNDY0NzA3NzNlNWFkY2MxMGM2YWRiODBjZjU2MGI2ZmNlMGE5NzU2MjdiZjllMTIyMGNhMjM1MTAxZmQ1MTEzOTQ2ZGZiYjZiOGZlMDAxZTIzZmQ0ZTBmNmRiZWUyZmQ3ZjIzYjRjNGY4MzYzZDljYjg0MDdlZTU4MTU2OTIxZGE4MWZlZjhjMmRhMWI5NGUxOWMzYjQ4Njg2NTdlNGQ2MGE2ZmEwMTI1NWM2MzQwNzYyMTViYzIxMTc5ZGFjOTU1MzNjN2U0Njk1MjE2YzA1OWU3NDczZjdkZGMzNThhNmJhZjRmN2FmMTllMDdkYjA2MDJiNTFmMjVmM2YxMjUyYjdkYjU2OTFhNDNjOGIyZWJjMzIxMDFlNmI3NWVjNDM4N2U5NDI1ZTliNGY4OTk2YmZmNTk1ODFkOTZiZTg5MDE4NTgyYzM0MDFjYjQ4NTM2OGI5ZWU1ZDFlMjRjYThlOWRlYjg0NiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NzMxIiwidGltZXN0YW1wIjoxNzU3MzM4MDUxNDE3LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6IjQxOWY2Mzc5LWU4NTEtNDI0YS1iOTkwLWMyNmE0M2I2NWU5MiIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6IjQxOWY2Mzc5LWU4NTEtNDI0YS1iOTkwLWMyNmE0M2I2NWU5MiIsInRva2VuVHlwZSI6IlJFRlJFU0hfVE9LRU4iLCJsaWZlVGltZSI6MTU1NTAwMDAwMDAsImlhdCI6MTc1NzMzODA1MSwiZXhwIjoxNzcyODg4MDUxfQ.yXVhLhED_2pZfpuSdFmBzToqhLwRSGEdTYyntN-5-ic	2025-09-18 17:27:49	2025-09-18 17:27:53	\N	[]	t	2025-09-08 16:57:57	2025-09-08 16:57:57
2	jibit	jibit	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJhdWQiOiJpZGVudGljYXRvciIsInN1YiI6IjRsbDVYIiwiYWNjZXNzIjp0cnVlLCJpc3MiOiJodHRwczovL2ppYml0LmlyIiwiZXhwIjoxNzU3NDI0NTk1fQ.g0PUOeWttNF3WorE0gVPg2x4F6WJeb5b7Be_f_ufsbqgOZaaIcH8lmh2NzztaZF_DOXgJbZZoV1-X7K1qpmrWw	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJhdWQiOiJpZGVudGljYXRvciIsInN1YiI6IjRsbDVYIiwiaXNzIjoiaHR0cHM6Ly9qaWJpdC5pciIsInJlZnJlc2giOnRydWUsImV4cCI6MTc1NzUxMDk5NX0.JDfkexxZuQfh_mUGnTzB5UUODDPwghXInWkaA7Vu4q_ZjwZQRfnitcyuKKbflYsfkssGadYYh6qIntfVonMlZg	2025-09-09 16:59:55	2025-09-10 16:59:55	2025-09-08 17:51:51	{"api_version":"v1","base_url":"https:\\/\\/napi.jibit.ir\\/ide"}	t	2025-07-01 09:13:36	2025-09-08 17:51:51
7	fino_kyc	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJlOWMyMjE1MmU1ODM0Zjg5Y2ZjNmY1MWQ1ZGNiOGQwMmY1OTRiNWZlMWYzODY0MDViNjQyZGVjM2ZmOWNjZWUzNDIxNDllMDE5YzEwOTAwMmJmYzUwN2UwNWI4NWM5YzdiMjA1NTUwNDg3NTdlZGQxOGI0NWZhMmU0ZGY4MzhjYWQ3YjdlNDM3ZTIxY2JmMDc3MzRiNzJlMmRhYTQ0YzI2NDlmNjI2ZGE5YjQ1MjM5ZDRlNjEwZjg0NjIwZDQwYjM5OGYyNjg5ZDNiNGJmMWJkZGM2YWE5OTA3MmY4ZjM1ZWM1OTRhYWNiZTkwMjMyZDkyZWFkNTc0YzQzODY0Y2QwYWZhY2ZjYjZmOTI4NzUyNWU1YTM1YjMyZDFmZjU5NzEwZTA4OGZmZDFkM2E0ZmY2MmFlZDQ3OWFjNTMwZmY3NDkzMTIwOWNiZTM2NjYxZWQ1ZjVlYTg0YjA3OTlmOGM1NWQ3YjE1ZjkzZjEyMTAzNDE1NjEzMjRhY2ZjZDM1M2Q1ZjhhMjFhYjYyMmI5ZTBmZWEwN2UyYTI1ZmJlNTI0MTBiNzcxMWExMGQwMGQzZjVhOWNlNjY5OWViNjBmZWM3NmRlZWFhYmRjYjYyYWQwYjgzZDRmNDA4YTNjOTdjYzlmMDFmMzU2MTg2YjM0Y2JjOWIyZWRkZmMwMDlkZmFlZGYzZjUzNjg2NjJkNTk3NmJkYWE1NDNkNTE3MzRkMmQzZTViZGYxYTM1YzdkMjU2MjM4M2UwNzUxYTE4YzM0NGE2MGE2OTY0YzEzNGRkNzJiMzRmNjFhMWNiMWZjNjFhMTNlNjFkMzliZmU0MjM2MjIzYjc2OTAyNWZkZmZlZjZjODdkMTdlYWI3NTM5NzM5NzI4MzY2YjFmM2E5OTA2N2I2NGUxNTJkY2FmZDhlMTdjZDg0ZmQ0YjEzZTdjYTY3ZDVjOWEyZDlhNzE3ZWNhOWNlYjYxOTIwMzQ5MmE5YTg2MWJlMmJmNTVjN2I0MjQ1ZDkwZjIwY2ZkY2Y1ODg2Y2U0ZDQ1MDExZmM2ZWY2NmZhNzEwMzAxNzc3MTkxOWI0YjlkNGM2YzlhZWMwY2I4MDNkNjc4YzA1MDkxMTQ3MDgxZDMwNGJkYzk1ZDczMzM0Zjc5OGRjM2JkYTQwOTEwZjJkOTJhY2ZmNzFlYWYyMTNjM2ZmMDg1YTdiOGNmNDFiYWMwNWVmMjNkMTQyYThjY2NhZDFiYWY5OGE0ZjUxZDVjNmMyZDc4YjYxYTUwMjQxMTQyMWM1NGNhMmYyZmQ2M2FhODkxOWNmZGZmMjE3YzM3ZWJhZjIzODQ4OWI4MGZmZDg3ZDM4Yjc3ODRkMjdjNGMxYzU1OTUwY2QyOGY5ZjA5NzZlNTkyMjgxODY1OTIyN2FjZmMzMjk0YzFhYjBkYzY1MGZmNjVmOWMxNWE0MmQ0M2FiNGVjNGFmYTE4OWY1OGU2NTA1MTExMWFiNzY0OWM5YTBhOGE5NGUxNjA0MzgzZWM1MzFiZTk3MThiOTZlYWI2OTVmMWI3ZmFiMzc5MWQ0OTVkMWNiYmNmY2ZmMTY1MzY0Y2NmYWIxNzMyZmZlOGVjYWQyZjliYmUzODRjM2YwYWZhZDZkMjA4ZGFkYmM5ZDc5YWFlNDExYzgwOTliMDA5NjRmMDA2ODNmZDUyN2Y5YzFjMDdiNGM0ZTNjOWUwZDQzMTA3MTMzZDBiNTExNzFhMzY1ODE2ZTJiM2Y4NGRhYTBjODVmY2I5NjU3MTY4MTU3YjljYWJlMWFiNmU1NGVmZGZhYzRmNzA4ZjMzMjI3YWQ2ODk4NDUwNThjYmQyNjRiOGUyMDk0NTM2NzFhZGRhYzY1MGIzYTNkYWYyNWIzODAzMDgzYTA2NTY4YTU2NzcwYmQ1YWNiMmUyZDNkYThjZjU4YWJjOGViMmUzMThiOWRkMzIyMDQ1YWMxNzM1NDcxZTM4NjAxODFiNzFkNjEyNDRlNzFhNDQ4NTI3OGE2OWI2ZjQxNjUwZTQ2MDUyODUyNWUxMzU3YmU1YWYzYzJmZmM2ZTNiMzI2NDVjNjgxMWVjYTg1NDBiYzk3OGE5MzUyMDA2ZWJkNmEzM2FmMjM0YmQxN2Q4YzVhMTBjYjZlNDk4MmVhZDdkNzc1ODRlMTQyNzhjNTVkNTFkMjFlY2NkYTIyNjRmMDJlOGRmNTg3N2Q0NmIxNjE0MjU5MjcyMzk0MjJkNjg5MGM4MTMwZmEyMTI4ZWZjZDEwZmUzMDZmOTA3ZDNmOWY2YzQwNTI1ZDJjZjgzNGVhODVlOWY1MzZiYzllYWQ4ODg2YzdiMWFjOGVhYjBkZDg0ODQ4ZWQxYjNkOGUxODk2M2Y5OTk3MTFmMjUwYjYyMjNjMzNlYmNiOTZlNjJjZGVkNjUxYjNkYTU5MDU4MmNkYWUzMmI1Yjc1Y2JkNDY0MTAwNjJjNGJlOGQ0OGYxZTAyNThmOTdiZmU4OTI4OTRiMzg3ZmNiYmMxNTI0ODRiOTViOGYyODZjMzI3MGQ4NGI0YWU0NmZiMTY2NmFjNGZkODRjMmUzNDhiNGFlMTEwYTA3N2IyOWZiN2QzZWMwM2Q3YWIzYjM5MzgxZjcyOGJlMDVmYTUwMzRkN2NmMTcyM2RmZGFmOWYyZWM1ZmM1YzM4ZGZmOWZiZTM5NzllM2FhYjdlYmI1NzIwN2FlNjY1YjM0OTQwNDc4YzdhZDQ4ZWNmMDYzMzQ0YWFmMDYwYWJhYWM3MDMwYTNkZjE1YjYyZWI3ZWI1MjY0MWY2MDcyNGU3MjI4N2U0MzZlN2QiLCJjcmVhdGlvbkRhdGUiOiIxNDA0MDYxNzE2NTg0MCIsInRpbWVzdGFtcCI6MTc1NzMzODEyMDM0NSwiZ3JhbnRUeXBlIjoiQ0xJRU5UX0NSRURFTlRJQUxTIiwicmVmcmVzaFRva2VuSWQiOiIzZTBiOTE5MS05ZDkyLTRiOWUtOGE4My0xMTUzYTM4MDQwYzEiLCJ2ZXJzaW9uIjoiNCIsInRva2VuSWQiOiI5MzVkM2ZmOC0xMzg0LTQ2MzktOTBiNS0xOWUxMTk0ZTEwMzEiLCJ0b2tlblR5cGUiOiJBQ0NFU1NfVE9LRU4iLCJsaWZlVGltZSI6ODY0MDAwMDAwLCJpYXQiOjE3NTczMzgxMjAsImV4cCI6MTc1ODIwMjEyMH0.kGkNnvEymvOnOCm976TB5wPFxkUMHCxJZo9KlVYVtjk	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJlOWMyMjE1MmU1ODM0Zjg5Y2ZjNmY1MWQ1ZGNiOGQwMmY1OTRiNWZlMWYzODY0MDViNjQyZGVjM2ZmOWNjZWUzNDIxNDllMDE5YzEwOTAwMmJmYzUwN2UwNWI4NWM5YzdiMjA1NTUwNDg3NTdlZGQxOGI0NWZhMmU0ZGY4MzhjYWQ3YjdlNDM3ZTIxY2JmMDc3MzRiNzJlMmRhYTQ0YzI2NDlmNjI2ZGE5YjQ1MjM5ZDRlNjEwZjg0NjIwZDQwYjM5OGYyNjg5ZDNiNGJmMWJkZGM2YWE5OTA3MmY4ZjM1ZWM1OTRhYWNiZTkwMjMyZDkyZWFkNTc0YzQzODY0Y2QwYWZhY2ZjYjZmOTI4NzUyNWU1YTM1YjMyZDFmZjU5NzEwZTA4OGZmZDFkM2E0ZmY2MmFlZDQ3OWFjNTMwZmY3NDkzMTIwOWNiZTM2NjYxZWQ1ZjVlYTg0YjA3OTlmOGM1NWQ3YjE1ZjkzZjEyMTAzNDE1NjEzMjRhY2ZjZDM1M2Q1ZjhhMjFhYjYyMmI5ZTBmZWEwN2UyYTI1ZmJlNTI0MTBiNzcxMWExMGQwMGQzZjVhOWNlNjY5OWViNjBmZWM3NmRlZWFhYmRjYjYyYWQwYjgzZDRmNDA4YTNjOTdjYzlmMDFmMzU2MTg2YjM0Y2JjOWIyZWRkZmMwMDlkZmFlZGYzZjUzNjg2NjJkNTk3NmJkYWE1NDNkNTE3MzRkMmQzZTViZGYxYTM1YzdkMjU2MjM4M2UwNzUxYTE4YzM0NGE2MGE2OTY0YzEzNGRkNzJiMzRmNjFhMWNiMWZjNjFhMTNlNjFkMzliZmU0MjM2MjIzYjc2OTAyNWZkZmZlZjZjODdkMTdlYWI3NTM5NzM5NzI4MzY2YjFmM2E5OTA2N2I2NGUxNTJkY2FmZDhlMTdjZDg0ZmQ0YjEzZTdjYTY3ZDVjOWEyZDlhNzE3ZWNhOWNlYjYxOTIwMzQ5MmE5YTg2MWJlMmJmNTVjN2I0MjQ1ZDkwZjIwY2ZkY2Y1ODg2Y2U0ZDQ1MDExZmM2ZWY2NmZhNzEwMzAxNzc3MTkxOWI0YjlkNGM2YzlhZWMwY2I4MDNkNjc4YzA1MDkxMTQ3MDgxZDMwNGJkYzk1ZDczMzM0Zjc5OGRjM2JkYTQwOTEwZjJkOTJhY2ZmNzFlYWYyMTNjM2ZmMDg1YTdiOGNmNDFiYWMwNWVmMjNkMTQyYThjY2NhZDFiYWY5OGE0ZjUxZDVjNmMyZDc4YjYxYTUwMjQxMTQyMWM1NGNhMmYyZmQ2M2FhODkxOWNmZGZmMjE3YzM3ZWJhZjIzODQ4OWI4MGZmZDg3ZDM4Yjc3ODRkMjdjNGMxYzU1OTUwY2QyOGY5ZjA5NzZlNTkyMjgxODY1OTIyN2FjZmMzMjk0YzFhYjBkYzY1MGZmNjVmOWMxNWE0MmQ0M2FiNGVjNGFmYTE4OWY1OGU2NTA1MTExMWFiNzY0OWM5YTBhOGE5NGUxNjA0MzgzZWM1MzFiZTk3MThiOTZlYWI2OTVmMWI3ZmFiMzc5MWQ0OTVkMWNiYmNmY2ZmMTY1MzY0Y2NmYWIxNzMyZmZlOGVjYWQyZjliYmUzODRjM2YwYWZhZDZkMjA4ZGFkYmM5ZDc5YWFlNDExYzgwOTliMDA5NjRmMDA2ODNmZDUyN2Y5YzFjMDdiNGM0ZTNjOWUwZDQzMTA3MTMzZDBiNTExNzFhMzY1ODE2ZTJiM2Y4NGRhYTBjODVmY2I5NjU3MTY4MTU3YjljYWJlMWFiNmU1NGVmZGZhYzRmNzA4ZjMzMjI3YWQ2ODk4NDUwNThjYmQyNjRiOGUyMDk0NTM2NzFhZGRhYzY1MGIzYTNkYWYyNWIzODAzMDgzYTA2NTY4YTU2NzcwYmQ1YWNiMmUyZDNkYThjZjU4YWJjOGViMmUzMThiOWRkMzIyMDQ1YWMxNzM1NDcxZTM4NjAxODFiNzFkNjEyNDRlNzFhNDQ4NTI3OGE2OWI2ZjQxNjUwZTQ2MDUyODUyNWUxMzU3YmU1YWYzYzJmZmM2ZTNiMzI2NDVjNjgxMWVjYTg1NDBiYzk3OGE5MzUyMDA2ZWJkNmEzM2FmMjM0YmQxN2Q4YzVhMTBjYjZlNDk4MmVhZDdkNzc1ODRlMTQyNzhjNTVkNTFkMjFlY2NkYTIyNjRmMDJlOGRmNTg3N2Q0NmIxNjE0MjU5MjcyMzk0MjJkNjg5MGM4MTMwZmEyMTI4ZWZjZDEwZmUzMDZmOTA3ZDNmOWY2YzQwNTI1ZDJjZjgzNGVhODVlOWY1MzZiYzllYWQ4ODg2YzdiMWFjOGVhYjBkZDg0ODQ4ZWQxYjNkOGUxODk2M2Y5OTk3MTFmMjUwYjYyMjNjMzNlYmNiOTZlNjJjZGVkNjUxYjNkYTU5MDU4MmNkYWUzMmI1Yjc1Y2JkNDY0MTAwNjJjNGJlOGQ0OGYxZTAyNThmOTdiZmU4OTI4OTRiMzg3ZmNiYmMxNTI0ODRiOTViOGYyODZjMzI3MGQ4NGI0YWU0NmZiMTY2NmFjNGZkODRjMmUzNDhiNGFlMTEwYTA3N2IyOWZiN2QzZWMwM2Q3YWIzYjM5MzgxZjcyOGJlMDVmYTUwMzRkN2NmMTcyM2RmZGFmOWYyZWM1ZmM1YzM4ZGZmOWZiZTM5NzllM2FhYjdlYmI1NzIwN2FlNjY1YjM0OTQwNDc4YzdhZDQ4ZWNmMDYzMzQ0YWFmMDYwYWJhYWM3MDMwYTNkZjE1YjYyZWI3ZWI1MjY0MWY2MDcyNGU3MjI4N2U0MzZlN2QiLCJjcmVhdGlvbkRhdGUiOiIxNDA0MDYxNzE2NTg0MCIsInRpbWVzdGFtcCI6MTc1NzMzODEyMDM0NSwiZ3JhbnRUeXBlIjoiQ0xJRU5UX0NSRURFTlRJQUxTIiwicmVmcmVzaFRva2VuSWQiOiIzZTBiOTE5MS05ZDkyLTRiOWUtOGE4My0xMTUzYTM4MDQwYzEiLCJ2ZXJzaW9uIjoiNCIsInRva2VuSWQiOiIzZTBiOTE5MS05ZDkyLTRiOWUtOGE4My0xMTUzYTM4MDQwYzEiLCJ0b2tlblR5cGUiOiJSRUZSRVNIX1RPS0VOIiwibGlmZVRpbWUiOjE1NTUwMDAwMDAwLCJpYXQiOjE3NTczMzgxMjAsImV4cCI6MTc3Mjg4ODEyMH0.umyd6N6Txa91JbkFqSO63q4ivhJGPqIzHiWWKBnrzCA	2025-09-18 17:28:59	2025-09-18 17:29:01	\N	[]	t	2025-09-08 16:59:04	2025-09-08 16:59:04
8	fino_inquiry	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJhODQyYTE1Mzk1MDM3YmE5Y2ZkN2E0YTg0ZGFhZjg5N2UxMDA3NTRiYWZmZDk1ZDFiMjk2MWE3MzdmNGQ0ZjczNDI3NDllMDBkYzkwZTBmNzRiNjUyNzcxY2JkMTRkODY2MjM1NDQ2MWY3MzZmYzA1NWY2NDdhM2UwYzhjOGNjZTY2OTY2MWE2NDIwZDFhOTdkNjZiNTI3MzRhZjFiOWI3ZGQ3NzQ2MWJmYjI0MjMxZDRiNTE4ZjMwODY1OWQ1ZDdkYzI2NzhiYzZiZWYxMGI4M2MwYTdjMDBhMmM5NjM5ZmU0NDU3ZWViOWRmMmIyZGQ5ZjhkOTcyOWYyZTIxY2UxZmJiOGE4NTY0ODk5MTA1NDg1ZTJiYmI2NDA1ZWNjOTU0Zjg4YmUyY2FkOGJjYjE2ZWI5ZGYyMGJlNDIxZWY3NDkzMTI0OTJiMjMyNzgxNmNmZWJlYTk2YmU2OWRkOGE1ZWNkZjgwYzk4ZWYyYjVhNTM0NzAyMjRhY2ZjZDc0YmRkZjhhODE5YTUyOGI5ZmJlNmUzN2YzZDc4ZTJmMzNmMGRlYjdmMWMwNTg2NDg3MjViYzdlYTM2OGVmMTUzYTE2ZWNlZmRiY2MxYmI2NmQ3ZjczMzU2MTQ4ODJiY2U4Y2RhNTFlMTVmMWY2MzJjYzc5MGJkYjJjZWM4MWRkMmUwY2EyNDE3NmU2YTc2NTAyOWFlZWQwYzNkMTYyODQxNzYyNjA4Y2IxYzM1ZGJjNjQ2NmJjN2Y4NjExZDE4ZDc1NGZmMGM3ODc5OGM3NmM1NmRiZDU3MmZlMDhlNWI4MDBhMWVhZDBjMzVhY2U5NzEyODM1YjcyYTVhMDhjM2IyZjQ5YzZlMGVlMmE2NDFjMTM3NjQ4MzY5ZjRiZmIzOWM2ZGZlNTQ0MDI3ZGZlOGRlNWVjMjk1ZTk0ZDRhZjc5ZDY1ZDE4MWExOGNhNDBjZmViMWMxZjk1NDJmMjhkY2ZhYTU3ZmE2MzJlNjU3MjQwODRjYzQ1YjY5YzBkYWY5ODIzMWUyOWEwYTFiZmQ3NWFmNmVhYjAxMmI1NTdhMTkwN2ZkYTk4ZmQzYzhmNzhiOGFjMzNjNjNkNTFjMGEwYjQ3NTMwYjcxNDM4ODk0OGMzZTMwYjc4MWQzMmZkMDU3OWEwMzJmZDVhZGFiN2RlZGZlMDg5N2IyMTE0MTYwZDVmYjE2ZjUwNWVlM2NjNTQ3ZjdkNjkxYzRiYmEwYzEwZTEyZDdjMDkxZDM5ZDI4YTE0ZjFlNDIyNmM4MDJhOGYxZjA3MmZlZGExM2RiZGFmNDE2ZDMyN2Y0ZjIyMDU0OWE5YmJjY2Y2YzJmYWQyNDQzMjFkMTk3ZjExYTFjODIzY2Y5ZjZmYzI2NTk3M2RhZDU1YTdkMjc5MTlmNjkxYTVjZmJkMzcxIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA2MTcxNjU5MzMiLCJ0aW1lc3RhbXAiOjE3NTczMzgxNzM3MzIsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiMGY3YjdkZWMtZmE5YS00ZDJkLWEzNTEtYWQxOTZiNGE3NzQ1IiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiMTBiYjk0NDgtNzNlOS00NzBkLWEyNjMtOWM0ZTI4MDA0NjQxIiwidG9rZW5UeXBlIjoiQUNDRVNTX1RPS0VOIiwibGlmZVRpbWUiOjg2NDAwMDAwMCwiaWF0IjoxNzU3MzM4MTczLCJleHAiOjE3NTgyMDIxNzN9.NTPwV_6984O-hENUhNJg_oaXe2HxlnEwRIj69bR40k4	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTJhODQyYTE1Mzk1MDM3YmE5Y2ZkN2E0YTg0ZGFhZjg5N2UxMDA3NTRiYWZmZDk1ZDFiMjk2MWE3MzdmNGQ0ZjczNDI3NDllMDBkYzkwZTBmNzRiNjUyNzcxY2JkMTRkODY2MjM1NDQ2MWY3MzZmYzA1NWY2NDdhM2UwYzhjOGNjZTY2OTY2MWE2NDIwZDFhOTdkNjZiNTI3MzRhZjFiOWI3ZGQ3NzQ2MWJmYjI0MjMxZDRiNTE4ZjMwODY1OWQ1ZDdkYzI2NzhiYzZiZWYxMGI4M2MwYTdjMDBhMmM5NjM5ZmU0NDU3ZWViOWRmMmIyZGQ5ZjhkOTcyOWYyZTIxY2UxZmJiOGE4NTY0ODk5MTA1NDg1ZTJiYmI2NDA1ZWNjOTU0Zjg4YmUyY2FkOGJjYjE2ZWI5ZGYyMGJlNDIxZWY3NDkzMTI0OTJiMjMyNzgxNmNmZWJlYTk2YmU2OWRkOGE1ZWNkZjgwYzk4ZWYyYjVhNTM0NzAyMjRhY2ZjZDc0YmRkZjhhODE5YTUyOGI5ZmJlNmUzN2YzZDc4ZTJmMzNmMGRlYjdmMWMwNTg2NDg3MjViYzdlYTM2OGVmMTUzYTE2ZWNlZmRiY2MxYmI2NmQ3ZjczMzU2MTQ4ODJiY2U4Y2RhNTFlMTVmMWY2MzJjYzc5MGJkYjJjZWM4MWRkMmUwY2EyNDE3NmU2YTc2NTAyOWFlZWQwYzNkMTYyODQxNzYyNjA4Y2IxYzM1ZGJjNjQ2NmJjN2Y4NjExZDE4ZDc1NGZmMGM3ODc5OGM3NmM1NmRiZDU3MmZlMDhlNWI4MDBhMWVhZDBjMzVhY2U5NzEyODM1YjcyYTVhMDhjM2IyZjQ5YzZlMGVlMmE2NDFjMTM3NjQ4MzY5ZjRiZmIzOWM2ZGZlNTQ0MDI3ZGZlOGRlNWVjMjk1ZTk0ZDRhZjc5ZDY1ZDE4MWExOGNhNDBjZmViMWMxZjk1NDJmMjhkY2ZhYTU3ZmE2MzJlNjU3MjQwODRjYzQ1YjY5YzBkYWY5ODIzMWUyOWEwYTFiZmQ3NWFmNmVhYjAxMmI1NTdhMTkwN2ZkYTk4ZmQzYzhmNzhiOGFjMzNjNjNkNTFjMGEwYjQ3NTMwYjcxNDM4ODk0OGMzZTMwYjc4MWQzMmZkMDU3OWEwMzJmZDVhZGFiN2RlZGZlMDg5N2IyMTE0MTYwZDVmYjE2ZjUwNWVlM2NjNTQ3ZjdkNjkxYzRiYmEwYzEwZTEyZDdjMDkxZDM5ZDI4YTE0ZjFlNDIyNmM4MDJhOGYxZjA3MmZlZGExM2RiZGFmNDE2ZDMyN2Y0ZjIyMDU0OWE5YmJjY2Y2YzJmYWQyNDQzMjFkMTk3ZjExYTFjODIzY2Y5ZjZmYzI2NTk3M2RhZDU1YTdkMjc5MTlmNjkxYTVjZmJkMzcxIiwiY3JlYXRpb25EYXRlIjoiMTQwNDA2MTcxNjU5MzMiLCJ0aW1lc3RhbXAiOjE3NTczMzgxNzM3MzIsImdyYW50VHlwZSI6IkNMSUVOVF9DUkVERU5USUFMUyIsInJlZnJlc2hUb2tlbklkIjoiMGY3YjdkZWMtZmE5YS00ZDJkLWEzNTEtYWQxOTZiNGE3NzQ1IiwidmVyc2lvbiI6IjQiLCJ0b2tlbklkIjoiMGY3YjdkZWMtZmE5YS00ZDJkLWEzNTEtYWQxOTZiNGE3NzQ1IiwidG9rZW5UeXBlIjoiUkVGUkVTSF9UT0tFTiIsImxpZmVUaW1lIjoxNTU1MDAwMDAwMCwiaWF0IjoxNzU3MzM4MTczLCJleHAiOjE3NzI4ODgxNzN9.lYAM4_t43uipcHHtz1j_QXF9_LNcwIVNG0rsyZfBQEI	2025-09-18 17:30:17	2025-09-18 17:30:21	\N	[]	t	2025-09-08 17:00:32	2025-09-08 17:00:32
9	fino_credit	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTI2OTcyNDRiMzQ0MTdkYjY4N2YxN2Y0Y2RiOTBhOTkyMzA1MzU3MTZiMmZhOTI0MTEwM2E3NmIwMzlmZmQxYTA3MTc3NTNhODBmYzgxMzU5NjJlNzUwNmYwZmJkMDNjMjZkMzc1MjU1MWUzZjcyYzc0YmVkMTRiNmZjODY4ZDg1YjAyZDJiNWI2ZTNlZGRiOTZkMjhlYzI1MmVhZTQ3OWE3Nzk4N2Y2YmE0ZjA1ODM2ZDFmYTE2ZTQwNTdkZDYxYTZmODEyOGMwYzlhNWUwMTZkMGQ3ZmU5ZTBmMzA4NzMyYTA0NDQ3ZWViMmM0NjczMDg2ZWRkNTY3ZDEzODY0Y2QwYWZhY2ZjYjZlOGY4ZDViNDU0ZjYxYjM2MjBkZWE4NTUzZTI4OGU4OTVkMWI4YjE2NmJmZGQ2OWUzNDAwZmExNDczZjYwOTBhMzNlNzAxNmNmYThiNzgyYmM3OGQzOTE0NGRjYjA0ODk1ZTYyODBjNTU1NjEzNzRlMWIyY2IxMGQxYTdiZjVhZmI2ZWY3ZmRlY2FhNzIyYzMyZTBlMTM1MTdiMDM1MTAxZmQ1MTEzOTQ2ZGZiYjZiOGZlMDAxZTIzZmM0ZmJmNGQxYmQzZjg0ZTYzMzQxNGY4YjYzY2VkYzk3MWRmNDU4MTk3ODMzODM4N2FjZTljNGM3NDJkZmEzY2YzZTUzNzU3MTJkNGU3Y2JhZmM1YTMzMTg2OTVjNzIyOTViY2M0ZTM3ZGJjMjU1MzNjYmJiNjMxYzEwZDE1OGI2NDY3NDYzZGYyZjhlNzBhNTA2NjVlYzk0NDk4ZjNlNWNmZTBjMmZiYmUyMDIyYjcyZjkyYTQ2MTM5ZWU3YWQ5MzM4NTViN2Y3MDhkMCIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTcwMDQ5IiwidGltZXN0YW1wIjoxNzU3MzM4MjQ5NDY3LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImViNmYwYzYwLTdiYTYtNDdiMS04ZTNmLWRlNDhjYmE4ODg5ZCIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6ImFlZjVmOThkLTQ4NmMtNGY5Yy1hMWY1LWZkOGE2Njg4NjFhMSIsInRva2VuVHlwZSI6IkFDQ0VTU19UT0tFTiIsImxpZmVUaW1lIjo4NjQwMDAwMDAsImlhdCI6MTc1NzMzODI0OSwiZXhwIjoxNzU4MjAyMjQ5fQ.SGkJEGuo18JALi8NtCgs0yXMM_RqmqclCPn2iGoVK84	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTI2OTcyNDRiMzQ0MTdkYjY4N2YxN2Y0Y2RiOTBhOTkyMzA1MzU3MTZiMmZhOTI0MTEwM2E3NmIwMzlmZmQxYTA3MTc3NTNhODBmYzgxMzU5NjJlNzUwNmYwZmJkMDNjMjZkMzc1MjU1MWUzZjcyYzc0YmVkMTRiNmZjODY4ZDg1YjAyZDJiNWI2ZTNlZGRiOTZkMjhlYzI1MmVhZTQ3OWE3Nzk4N2Y2YmE0ZjA1ODM2ZDFmYTE2ZTQwNTdkZDYxYTZmODEyOGMwYzlhNWUwMTZkMGQ3ZmU5ZTBmMzA4NzMyYTA0NDQ3ZWViMmM0NjczMDg2ZWRkNTY3ZDEzODY0Y2QwYWZhY2ZjYjZlOGY4ZDViNDU0ZjYxYjM2MjBkZWE4NTUzZTI4OGU4OTVkMWI4YjE2NmJmZGQ2OWUzNDAwZmExNDczZjYwOTBhMzNlNzAxNmNmYThiNzgyYmM3OGQzOTE0NGRjYjA0ODk1ZTYyODBjNTU1NjEzNzRlMWIyY2IxMGQxYTdiZjVhZmI2ZWY3ZmRlY2FhNzIyYzMyZTBlMTM1MTdiMDM1MTAxZmQ1MTEzOTQ2ZGZiYjZiOGZlMDAxZTIzZmM0ZmJmNGQxYmQzZjg0ZTYzMzQxNGY4YjYzY2VkYzk3MWRmNDU4MTk3ODMzODM4N2FjZTljNGM3NDJkZmEzY2YzZTUzNzU3MTJkNGU3Y2JhZmM1YTMzMTg2OTVjNzIyOTViY2M0ZTM3ZGJjMjU1MzNjYmJiNjMxYzEwZDE1OGI2NDY3NDYzZGYyZjhlNzBhNTA2NjVlYzk0NDk4ZjNlNWNmZTBjMmZiYmUyMDIyYjcyZjkyYTQ2MTM5ZWU3YWQ5MzM4NTViN2Y3MDhkMCIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTcwMDQ5IiwidGltZXN0YW1wIjoxNzU3MzM4MjQ5NDY4LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImViNmYwYzYwLTdiYTYtNDdiMS04ZTNmLWRlNDhjYmE4ODg5ZCIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6ImViNmYwYzYwLTdiYTYtNDdiMS04ZTNmLWRlNDhjYmE4ODg5ZCIsInRva2VuVHlwZSI6IlJFRlJFU0hfVE9LRU4iLCJsaWZlVGltZSI6MTU1NTAwMDAwMDAsImlhdCI6MTc1NzMzODI0OSwiZXhwIjoxNzcyODg4MjQ5fQ.S2GTnc9_jilsPwxlrUvuGRCxu_JkQdcur5rZGFTaOaQ	2025-09-18 17:30:59	2025-09-18 17:31:02	\N	[]	t	2025-09-08 17:01:14	2025-09-08 17:01:14
3	fino_sms	finnotech	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTIzODQyMjQ2MzE1YzMzYWNkNWYyNjc1N2QwZDJiZTk4MjQ0ODFmNGJiNGU3OTcxNjU1NmE2OWViM2RmM2M5ZWIyOTJjMGFiYzAzYzIxNDQyMmNmMTQ5NjI0M2IwNDBjMDdkMmM0OTRkNDg3NTdlZGQxOGI0NWZhMmU0ZGY4MzhjYWQ3YjdlNDM2YjI1ZDZiMzZiMjhiMzNlMzJlNjU5OWI2MmMwNjA2YmJjYTkxMzc0ODJlOTFlZjUxNTJiZDgwYjYyOTk3Njg3YzliMmVjMDRkYzhlYjc4MDFkNzM4MTMzZjkwZjdlZWZmMmM4MzkyNjkxZDZkODI0ODcyMDMzOTE0Y2VjZDFkYzM5Y2FkYzBkMGU0NiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NTIwIiwidGltZXN0YW1wIjoxNzU3MzM3OTIwNzY0LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImU3OWE0MmI1LWEwYmMtNDcwMi1iYzBhLTY4ZjFiMmRkNjI1ZiIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6IjFkYTRjYzVhLTFjNDktNDFiOC1iMTExLWNhMGYzZmVlY2U0YiIsInRva2VuVHlwZSI6IkFDQ0VTU19UT0tFTiIsImxpZmVUaW1lIjo4NjQwMDAwMDAsImlhdCI6MTc1NzMzNzkyMCwiZXhwIjoxNzU4MjAxOTIwfQ.8tNgwXWTcBYCy4E1x9ISk-dPX1KfmD7DDcfRn1bJ2_s	eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiYW5rIjoiMDYyIiwiY2xpZW50SWQiOiJwaXNoa2hhbmFrIiwiY2xpZW50SW5mbyI6IjMzOGRmMmJmMjNkZjc1ZGQzNTc2MzI3MTIzODQyMjQ2MzE1YzMzYWNkNWYyNjc1N2QwZDJiZTk4MjQ0ODFmNGJiNGU3OTcxNjU1NmE2OWViM2RmM2M5ZWIyOTJjMGFiYzAzYzIxNDQyMmNmMTQ5NjI0M2IwNDBjMDdkMmM0OTRkNDg3NTdlZGQxOGI0NWZhMmU0ZGY4MzhjYWQ3YjdlNDM2YjI1ZDZiMzZiMjhiMzNlMzJlNjU5OWI2MmMwNjA2YmJjYTkxMzc0ODJlOTFlZjUxNTJiZDgwYjYyOTk3Njg3YzliMmVjMDRkYzhlYjc4MDFkNzM4MTMzZjkwZjdlZWZmMmM4MzkyNjkxZDZkODI0ODcyMDMzOTE0Y2VjZDFkYzM5Y2FkYzBkMGU0NiIsImNyZWF0aW9uRGF0ZSI6IjE0MDQwNjE3MTY1NTIwIiwidGltZXN0YW1wIjoxNzU3MzM3OTIwNzY0LCJncmFudFR5cGUiOiJDTElFTlRfQ1JFREVOVElBTFMiLCJyZWZyZXNoVG9rZW5JZCI6ImU3OWE0MmI1LWEwYmMtNDcwMi1iYzBhLTY4ZjFiMmRkNjI1ZiIsInZlcnNpb24iOiI0IiwidG9rZW5JZCI6ImU3OWE0MmI1LWEwYmMtNDcwMi1iYzBhLTY4ZjFiMmRkNjI1ZiIsInRva2VuVHlwZSI6IlJFRlJFU0hfVE9LRU4iLCJsaWZlVGltZSI6MTU1NTAwMDAwMDAsImlhdCI6MTc1NzMzNzkyMCwiZXhwIjoxNzcyODg3OTIwfQ.JZhXlAGAmbwauekS97OQpxIwT4oxuzAUhNbSf1NgbXg	2025-09-18 17:25:32	2025-09-18 17:25:35	2025-09-08 20:21:12	[]	t	2025-09-08 16:55:46	2025-09-08 20:21:12
\.


--
-- Data for Name: transactions; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.transactions (id, payable_type, payable_id, wallet_id, type, amount, confirmed, meta, uuid, created_at, updated_at, deleted_at) FROM stdin;
1	App\\Models\\User	2	1	deposit	10000000	t	{"description":"\\u0628\\u0627\\u0632\\u06af\\u0631\\u062f\\u0627\\u0646\\u06cc \\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u067e\\u0633 \\u0627\\u0632 \\u0628\\u0627\\u0632\\u06cc\\u0627\\u0628\\u06cc \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"restoration_credit","admin_action":true}	cd3e3253-2a41-4063-9aa7-6fa1d7e64ee0	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
2	App\\Models\\User	4	2	deposit	10000000	t	{"description":"\\u0628\\u0627\\u0632\\u06af\\u0631\\u062f\\u0627\\u0646\\u06cc \\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u067e\\u0633 \\u0627\\u0632 \\u0628\\u0627\\u0632\\u06cc\\u0627\\u0628\\u06cc \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"restoration_credit","admin_action":true}	a4b505f6-156c-4d09-b2b7-829a438287e0	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
3	App\\Models\\User	5	3	deposit	10000000	t	{"description":"\\u0628\\u0627\\u0632\\u06af\\u0631\\u062f\\u0627\\u0646\\u06cc \\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u067e\\u0633 \\u0627\\u0632 \\u0628\\u0627\\u0632\\u06cc\\u0627\\u0628\\u06cc \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"restoration_credit","admin_action":true}	a93d6fcc-51eb-4cdb-87cc-99d839c57e3d	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
4	App\\Models\\User	6	4	deposit	10000000	t	{"description":"\\u0628\\u0627\\u0632\\u06af\\u0631\\u062f\\u0627\\u0646\\u06cc \\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u067e\\u0633 \\u0627\\u0632 \\u0628\\u0627\\u0632\\u06cc\\u0627\\u0628\\u06cc \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"restoration_credit","admin_action":true}	c2236b25-657f-4def-b30a-b69331d26be6	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
5	App\\Models\\User	7	5	deposit	10000000	t	{"description":"\\u0628\\u0627\\u0632\\u06af\\u0631\\u062f\\u0627\\u0646\\u06cc \\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u067e\\u0633 \\u0627\\u0632 \\u0628\\u0627\\u0632\\u06cc\\u0627\\u0628\\u06cc \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"restoration_credit","admin_action":true}	7b7f6838-1cd1-4e7b-b747-89b22b0e3b88	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
6	App\\Models\\User	9	6	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	155fe52f-bf5b-47e7-8511-18ee197ca711	2025-09-08 17:03:35	2025-09-08 17:03:35	\N
7	App\\Models\\User	10	7	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	dcbee8ea-a419-44f4-bd4a-98b658b742ec	2025-09-08 17:03:35	2025-09-08 17:03:35	\N
8	App\\Models\\User	11	8	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2803dcb3-f4c2-4bfa-a109-52cdffd8ac48	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
9	App\\Models\\User	12	9	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	0a7f8afe-7751-4a29-9dc4-728926336787	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
10	App\\Models\\User	13	10	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9bbb9635-f4f5-463f-b18e-f4c855c8afbd	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
11	App\\Models\\User	14	11	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f124efd3-306c-48c6-8640-49762d711f4b	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
12	App\\Models\\User	15	12	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	0f8a147b-8945-4295-8207-5380c9c6b091	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
13	App\\Models\\User	16	13	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	713cad46-263b-4a07-bcef-24a33640ae70	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
14	App\\Models\\User	17	14	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b07f7cc8-1f51-4975-8a04-87cb01277925	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
15	App\\Models\\User	18	15	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2b7f0164-0a9c-4657-ab1c-b1a4b98b72f8	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
16	App\\Models\\User	19	16	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c86f2e81-2cf5-4918-9d3c-4e2e9031b168	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
17	App\\Models\\User	20	17	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2c2c1f37-de62-4c45-9a92-08fc15893570	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
18	App\\Models\\User	21	18	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b7b020bd-84dc-4daf-9dd5-70b2aa729451	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
19	App\\Models\\User	22	19	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4eb76fd3-cc27-4c07-b975-a4260532178b	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
20	App\\Models\\User	23	20	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	a74993d0-192f-4075-befb-693ecbe66719	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
21	App\\Models\\User	24	21	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	05645a54-8fb0-4dab-be92-d790508e75e4	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
22	App\\Models\\User	25	22	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5d58c92f-b17f-4554-a588-9fbddef133d6	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
23	App\\Models\\User	26	23	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	a45448a5-e235-4302-9dd9-dd83b515f996	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
24	App\\Models\\User	27	24	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	ce506f16-1418-4dd8-a2fc-fdeb69161c9c	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
25	App\\Models\\User	28	25	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5bfe540e-358e-40a9-957d-0f0e1936466f	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
26	App\\Models\\User	29	26	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	12eb1d3d-b657-4e2c-9aec-ed1f9eee1ab6	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
27	App\\Models\\User	30	27	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c2da77e1-b58f-4b5b-88c6-b8e53d7766dd	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
28	App\\Models\\User	31	28	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	0cd1ebfd-e96b-45d0-95d2-fab51a4f8c72	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
29	App\\Models\\User	32	29	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7eae7575-c6fa-4258-8802-e693769f0823	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
30	App\\Models\\User	33	30	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f79c0753-7c22-48fc-afc3-4f740ebceeff	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
31	App\\Models\\User	34	31	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	72eada1c-a928-4626-af78-549e63840c22	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
32	App\\Models\\User	35	32	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	057cff18-3b2b-4386-890c-296b3f8e9829	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
33	App\\Models\\User	36	33	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4f2ee159-f384-4ba5-ba93-4219b2b005c4	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
34	App\\Models\\User	37	34	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9bd70fd9-4770-48c1-aa6e-677695152f7e	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
35	App\\Models\\User	38	35	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5ea4b31a-59d0-4633-8cf7-6e5d9905a03c	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
36	App\\Models\\User	39	36	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8025d4e3-97a6-4c42-9156-6cc75f0522f7	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
37	App\\Models\\User	40	37	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9d7131ba-8b80-483b-bd53-34b1cbdf3b89	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
38	App\\Models\\User	41	38	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e2813b65-41d9-4aff-b4cc-7c9a9d85f30b	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
39	App\\Models\\User	42	39	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c3ba2278-8471-45ed-9e71-0c33f19b49dc	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
40	App\\Models\\User	43	40	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2018c8ca-bbd3-4d63-9dac-a4a32c1f0831	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
41	App\\Models\\User	44	41	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	dbd61c6f-7ffe-4b10-944e-b719df3b39af	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
42	App\\Models\\User	45	42	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	714418f7-1198-4281-acbe-13e66b646abc	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
43	App\\Models\\User	46	43	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	a1a4a5e1-2e1c-49b8-90ed-d70152b95ba6	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
44	App\\Models\\User	47	44	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8031a712-7af1-4d0f-94df-a420aec7b581	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
45	App\\Models\\User	48	45	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e95acc59-285f-4d06-bf5f-e5b99fce892c	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
46	App\\Models\\User	49	46	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	31c94fb2-c9d2-4216-a966-705b34ea40bf	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
47	App\\Models\\User	50	47	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	cdf670e8-f38c-4127-b591-6ca0c42c34f6	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
48	App\\Models\\User	51	48	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f6cdab74-b2a8-409e-8964-470a0e47746a	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
49	App\\Models\\User	52	49	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7a221e86-ee66-43a9-a756-d9953f0a4a82	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
50	App\\Models\\User	53	50	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	927e3201-fc6f-4c8d-9937-5d175e3a8b08	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
51	App\\Models\\User	54	51	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b198d56b-f043-4dcf-9761-394a45e26602	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
52	App\\Models\\User	55	52	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7cb0d96b-70df-4337-82e7-23b3528b9747	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
53	App\\Models\\User	56	53	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7525035d-04ee-4092-a767-2cad3345c570	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
54	App\\Models\\User	57	54	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b62fb65c-b3bd-47a4-9555-7844b729f8b8	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
55	App\\Models\\User	58	55	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b8b72369-f3df-4a1d-87c7-4cf60b21640e	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
56	App\\Models\\User	59	56	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7fd6767c-1750-47fd-b794-4c37d34a61a6	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
57	App\\Models\\User	60	57	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d96c9a5d-7ff9-418b-80cf-87986480b17b	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
58	App\\Models\\User	61	58	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	fdb18e23-e520-43c4-9c28-f931835c5afe	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
59	App\\Models\\User	62	59	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	16140d04-e2b9-490e-b917-8d87b99d393f	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
60	App\\Models\\User	63	60	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	63d8216c-a654-44d7-85ef-9bd8a253a92c	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
61	App\\Models\\User	64	61	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	02052f51-ab26-4bb6-ac86-cbab8125317d	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
62	App\\Models\\User	65	62	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3e451d61-7b37-48e5-892e-6c45c4ad8805	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
63	App\\Models\\User	66	63	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	46d1aa63-d39b-4343-b792-2dd7902c74a3	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
64	App\\Models\\User	67	64	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d88a800f-1126-42b5-ae26-3ec1b5ac13b4	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
65	App\\Models\\User	68	65	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3086e8aa-8df2-45fa-8657-ada6972271b6	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
66	App\\Models\\User	69	66	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	dadcdea2-2f58-4f31-9b4b-d70611fd140e	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
67	App\\Models\\User	70	67	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3085639c-ec8b-4f3a-b179-edcfd6de9168	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
68	App\\Models\\User	71	68	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b4ce8da6-257f-4632-874c-df8bd5acbbb0	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
69	App\\Models\\User	72	69	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e5d7a353-21f3-4e6e-be72-8b806b70fd21	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
70	App\\Models\\User	73	70	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c8f89af9-959b-4ddb-8aa6-796808149b2e	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
71	App\\Models\\User	74	71	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3232e233-9c53-4c3d-8cf7-755c23b8039e	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
72	App\\Models\\User	75	72	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	a7137a18-7cf7-469c-8b79-3a1b83b34648	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
73	App\\Models\\User	76	73	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9ca267c7-f2d4-452d-9cca-9cee3d165998	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
74	App\\Models\\User	77	74	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	dcaa900e-6404-4e40-b980-59c4667be1b4	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
75	App\\Models\\User	78	75	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d67fbc88-5106-4d4b-bc2d-5dbf76e05152	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
76	App\\Models\\User	79	76	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4258080e-6f60-4471-84fc-d9bcc0096b29	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
77	App\\Models\\User	80	77	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	ec875a70-a1f4-4355-9e6e-e39b6eee8dae	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
78	App\\Models\\User	81	78	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e570aee4-7572-42ae-940d-47314204ce3a	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
79	App\\Models\\User	82	79	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9ee3259a-fee9-4c11-a96b-d4c82c07e762	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
80	App\\Models\\User	83	80	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8ee29ba2-0444-4c2d-9ffc-446a46ddf7ac	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
81	App\\Models\\User	84	81	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f57bd556-63f0-416b-8395-05fcee6eebf9	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
82	App\\Models\\User	85	82	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4a90e1f7-bc25-48e2-baaf-ecf60e8c740f	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
83	App\\Models\\User	86	83	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	032f8595-39c4-4140-b7c5-b68f6defad71	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
84	App\\Models\\User	87	84	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	612ed98f-59c6-4e6a-b10a-b00332cb7db1	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
85	App\\Models\\User	88	85	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	7ae67fcf-9063-46cc-bab5-fd7711afa45a	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
86	App\\Models\\User	89	86	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	6a502290-b7ff-485c-ae0f-dc210baa2764	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
87	App\\Models\\User	90	87	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5cf76719-0717-4c25-a2c1-9576e81579b9	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
88	App\\Models\\User	91	88	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8e10c8e5-5853-4f08-b6f1-c6971d1ad3a0	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
89	App\\Models\\User	92	89	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	09105e73-7b5f-4ca4-bcf1-84aa2363ed4d	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
90	App\\Models\\User	93	90	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b506afa2-23c3-47e9-8362-733a3d2baee9	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
91	App\\Models\\User	94	91	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	529e7805-a3c7-413b-bb25-74de81f2b5f7	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
92	App\\Models\\User	95	92	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4f610714-a5f7-41cb-84d8-615803739742	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
93	App\\Models\\User	96	93	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c882ddbc-448e-4833-853e-b7e4899cf18c	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
94	App\\Models\\User	97	94	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c6e7733f-6c20-450e-ae77-df784086028e	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
95	App\\Models\\User	98	95	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	a1f824f6-cf41-4d7c-b594-b4dc74599f4d	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
96	App\\Models\\User	99	96	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	62dd60e7-b52e-496b-ae6c-322e46f52693	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
97	App\\Models\\User	100	97	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	6b04b142-05bd-4693-ac16-eef4126ec496	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
98	App\\Models\\User	101	98	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b9854bab-4b52-43c3-b44e-ccd264ee5b0d	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
99	App\\Models\\User	102	99	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	012ccf36-cfa0-4439-911f-172aacd89dca	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
100	App\\Models\\User	103	100	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	edf84ca4-9c05-433e-bccf-087c5b7ae48f	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
101	App\\Models\\User	104	101	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d8f51358-5240-4ba3-8319-c87e5598972e	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
102	App\\Models\\User	105	102	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3a17676d-55a8-4a03-aef8-34479dfed643	2025-09-08 17:04:01	2025-09-08 17:04:01	\N
103	App\\Models\\User	106	103	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5d4f481a-38ff-41e9-919b-58de93d57714	2025-09-08 17:04:01	2025-09-08 17:04:01	\N
104	App\\Models\\User	107	104	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	de89b609-b4bb-4ed8-9c42-3c7f86c55186	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
105	App\\Models\\User	108	105	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c7093a2f-dcbc-4258-a47f-98954e8a67d1	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
106	App\\Models\\User	109	106	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	970e6cf6-d50e-46cf-a07d-c8caba142755	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
107	App\\Models\\User	110	107	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	ca301693-109f-42cc-895a-6beaeb0a572c	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
108	App\\Models\\User	111	108	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	650d865e-d65d-46b8-98a6-e77c8bbc0b57	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
109	App\\Models\\User	112	109	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3423590c-e065-460f-8c3f-0e7cd57e2756	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
110	App\\Models\\User	113	110	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f6840f9d-74d0-497c-b645-432e41a76d36	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
111	App\\Models\\User	114	111	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c85491a5-df03-4f37-92e7-d3a7a58a4380	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
112	App\\Models\\User	115	112	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	98850246-a329-422d-9a00-a7238b3b34a4	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
113	App\\Models\\User	116	113	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2ec3f5f0-b8e9-443b-b914-720725abcd13	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
114	App\\Models\\User	117	114	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e334b7ad-7aa4-44e2-89e6-c03165b1c953	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
115	App\\Models\\User	118	115	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9cd840df-f8e2-4cd1-9644-be46e3816be4	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
116	App\\Models\\User	119	116	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8b1c5ba0-a315-474e-8ad1-0199e03bdc5b	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
117	App\\Models\\User	120	117	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	23a5b085-aa44-43d1-be4a-aaa1e8f9433f	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
118	App\\Models\\User	121	118	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	389b8b0e-bde3-4081-b6df-7f15fdce916e	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
119	App\\Models\\User	122	119	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	55df1c9f-3499-44bf-a365-9251c1efafb3	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
120	App\\Models\\User	123	120	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	017edf97-7067-491e-a36f-600d38e94b28	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
121	App\\Models\\User	124	121	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	aeb6d5e5-e516-4081-9b77-70b81fba4f87	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
122	App\\Models\\User	125	122	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	aa266094-f26f-4540-baa9-248311e71bbc	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
123	App\\Models\\User	126	123	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	53f4d1a1-c495-47f4-91ed-3e1cba5a841c	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
124	App\\Models\\User	127	124	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9afe0568-1a62-4180-afdd-774ddc27e51f	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
125	App\\Models\\User	128	125	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3aecec86-9d67-4e21-98ae-132dab810200	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
126	App\\Models\\User	129	126	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	49c3aff5-c4ce-4b86-8b79-adcf38d410f4	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
127	App\\Models\\User	130	127	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	0f182563-5854-4406-8d6f-d5c5b3b8af56	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
128	App\\Models\\User	131	128	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	5fd3cd47-5b49-4268-b422-be34066240dc	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
129	App\\Models\\User	132	129	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3f203117-5b0c-4275-ba54-fca9a6ab2e1a	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
130	App\\Models\\User	133	130	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	73a7cdb7-a1d7-4114-a9e0-10d1bb8e5898	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
131	App\\Models\\User	134	131	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	32e63e36-13d9-449b-a3ea-ef2027309ff5	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
132	App\\Models\\User	135	132	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d56e5fb4-ee98-4e8a-98c2-6b0e4e25d4f7	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
133	App\\Models\\User	136	133	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	40231b0f-eec0-40a7-9e7a-5dd1294a2bae	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
134	App\\Models\\User	137	134	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	246022b8-65e4-48f0-9cb2-a9d29573bcac	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
135	App\\Models\\User	138	135	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	70fecf74-ab56-493d-a5df-7b82fdb43c05	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
136	App\\Models\\User	139	136	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	203913a0-6198-4348-8fd1-8f425e3049a7	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
137	App\\Models\\User	140	137	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c0aae643-544d-4298-9d89-218004c12287	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
138	App\\Models\\User	141	138	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	11feb962-abd6-475d-a457-cf4620652d93	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
139	App\\Models\\User	142	139	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c34cdca7-9fdc-4c00-92fc-13c4a5ef5411	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
140	App\\Models\\User	143	140	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f3efe697-909a-4d23-89a8-384a19fe3b47	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
141	App\\Models\\User	144	141	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	cc8008d1-9473-4eea-8f5d-8f1feb66dc7a	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
142	App\\Models\\User	145	142	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	fb44a973-3843-4d71-974c-4f2b6a418357	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
143	App\\Models\\User	146	143	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4bf772a9-fc14-4f9b-89e6-2922ada03bf1	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
144	App\\Models\\User	147	144	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2d8b1858-649a-4eec-8e9b-f8b588250d96	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
145	App\\Models\\User	148	145	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f711a182-2937-40c5-86cb-8938f002782d	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
146	App\\Models\\User	149	146	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	e2c56e61-cf5a-4807-ae9f-9ce60c38ea37	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
147	App\\Models\\User	150	147	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f71833f9-c09b-4732-95c1-eda1be342525	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
148	App\\Models\\User	151	148	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	512aa984-15a7-4e91-91ea-4b75f8c32962	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
149	App\\Models\\User	152	149	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c9c3705c-aa3f-443f-b1ef-02ba5eeec454	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
150	App\\Models\\User	153	150	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	3bd54e7e-c7cf-4163-be46-63f1ea08bb7d	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
151	App\\Models\\User	154	151	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	8da6fb85-d17c-4811-9eb8-4b2b16944954	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
152	App\\Models\\User	155	152	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	d3d5c4a2-e640-47f4-8897-7506eef999b5	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
153	App\\Models\\User	156	153	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	c82f7bb5-df3b-4147-93a1-f79cbad30050	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
154	App\\Models\\User	157	154	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	9e4dadd1-84b8-4a31-98b9-6ddc798ff0bf	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
155	App\\Models\\User	158	155	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	f1599b08-4d03-4ae1-99c8-01d0c9614f85	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
156	App\\Models\\User	159	156	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	fbf261a5-d01f-43c5-8b13-c1aa170a1c4c	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
157	App\\Models\\User	160	157	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	ff8fe4f3-f6b8-4983-b1d6-0409787e2a9f	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
158	App\\Models\\User	161	158	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	4a267534-74af-4f18-951e-bf896d92f74f	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
159	App\\Models\\User	162	159	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	967a8ce5-24d5-4762-b9b6-ec67a6ffacfe	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
160	App\\Models\\User	163	160	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	0cff37e7-0b33-4c5a-93df-a2af45ef5b4b	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
161	App\\Models\\User	164	161	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	2ab5b3c7-65f1-44f2-9ada-ca5e1a4000e1	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
162	App\\Models\\User	165	162	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	62858525-4666-4dd2-8c91-6e5469c9573e	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
163	App\\Models\\User	166	163	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	43903294-71d7-47a2-b92a-52222a751c8e	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
164	App\\Models\\User	167	164	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	1914d698-b0d8-463c-af82-c67da9130a48	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
165	App\\Models\\User	168	165	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	af49bad7-ede3-495b-aa41-2d3dc88d3d93	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
166	App\\Models\\User	169	166	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	b3b62e28-abee-4ee4-ba53-fdbd3193e451	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
167	App\\Models\\User	170	167	deposit	10000000	t	{"description":"\\u0627\\u0639\\u062a\\u0628\\u0627\\u0631 \\u0627\\u0648\\u0644\\u06cc\\u0647 \\u062d\\u0633\\u0627\\u0628 \\u06a9\\u0627\\u0631\\u0628\\u0631\\u06cc","type":"initial_credit","source":"log_recovery"}	108ce2d4-6de3-4629-ab3a-a1a8e36149a7	2025-09-08 17:04:19	2025-09-08 17:04:19	\N
\.


--
-- Data for Name: transfers; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.transfers (id, from_id, to_id, status, status_last, deposit_id, withdraw_id, discount, fee, uuid, created_at, updated_at, deleted_at, extra) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, google_id, github_id, facebook_id, discord_id, mobile, mobile_verified_at) FROM stdin;
1	مدیر سیستم	admin@pishkhanak.com	\N	$2y$12$SwTKUFX/AJQWibeoi4UQt.Q12JBYTjcz4BujZ8ahWYuQYgZXINu.e	\N	2025-09-08 16:30:37	2025-09-08 16:30:37	\N	\N	\N	\N	\N	\N
2	کاربر 0555	09001790555@mobile.pishkhanak.com	\N	\N	nUtehPkwdau3qNwc2pFahWJUX5yx14xz8xmuHtIL9bmEsHiZe1JoF8ZnU11A	2025-09-08 16:31:13	2025-09-08 16:31:13	\N	\N	\N	\N	09001790555	2025-09-08 16:31:13
3	سیدعلی خوشدل	khoshdel.net@gmail.com	\N	$2y$12$DB2hQ2kNgd0A2ytwjLAs8.uun97hhjJ9ODPfa6noELgdFtpH8wAAS	\N	2025-09-08 16:35:16	2025-09-08 16:35:16	\N	\N	\N	\N	\N	\N
4	کاربر 0913	09352290913@mobile.pishkhanak.com	\N	\N	ivdPfrOXZaaVwIkf2enviSEZyAdEafg4jlqFN6PPjnB1ZTXB8hRtunGueJAo	2025-09-08 16:49:48	2025-09-08 16:49:48	\N	\N	\N	\N	09352290913	2025-09-08 16:49:48
6	کاربر 9742	09122239742@mobile.pishkhanak.com	\N	$2y$12$fqXZVSJ07FfqvWdehesgSu9kowskewA1Co1doejYvrC3zbnsz2VUu	\N	2025-09-08 16:52:27	2025-09-08 16:52:27	\N	\N	\N	\N	09122239742	2025-07-14 14:01:49
7	کاربر 5864	09104775864@mobile.pishkhanak.com	\N	$2y$12$Mx9GpVEmN1Rks3neQNTKQe6THI0xII47iAQapngRSAZHdln108AZC	\N	2025-09-08 16:52:27	2025-09-08 16:52:27	\N	\N	\N	\N	09104775864	2025-07-15 13:35:49
35	کاربر 0587	09120720587@mobile.pishkhanak.com	\N	$2y$12$sXco0LCRkwV4GhvMZvj34eiNB0QcIcgDuvg2ZVUbJcJMFV8tGgo.O	\N	2025-09-08 17:03:42	2025-09-08 17:03:42	\N	\N	\N	\N	09120720587	2025-09-08 17:03:42
8	کاربر 2141	09334172141@mobile.pishkhanak.com	\N	\N	0algzpdepUVMQ3JzHdvbA6xxExaVwnutXkwyq4taLBQZh9HKNLHzVcWh53Tj	2025-09-08 16:58:14	2025-09-08 16:58:14	\N	\N	\N	\N	09334172141	2025-09-08 16:58:14
39	کاربر 6186	09121126186@mobile.pishkhanak.com	\N	$2y$12$2UmKiq3PLCadq9lFAi2O5ODTZyJsOWpfyaSHmknaivOxdGXWYtniq	BrjfDrcz9D70v1AwlRM0b4rQviCyz4y68uXSJvU8N0ZIVGoQvskF2Rwrv8Zp	2025-09-08 17:03:43	2025-09-08 17:03:43	\N	\N	\N	\N	09121126186	2025-09-08 17:03:43
9	کاربر 9232	09001519232@mobile.pishkhanak.com	\N	$2y$12$uUbeNZqlK3gp57k/rRDdluVNbaBSfdU2sTo0PyybN0BFHJNaa8Rr.	\N	2025-09-08 17:03:35	2025-09-08 17:03:35	\N	\N	\N	\N	09001519232	2025-09-08 17:03:35
10	کاربر 5807	09010535807@mobile.pishkhanak.com	\N	$2y$12$wZaSzlM4mcbcIP9Pa9Y4Ye6hGqQgZ9yMnAUu15O51F41.LDc/GmCm	\N	2025-09-08 17:03:35	2025-09-08 17:03:35	\N	\N	\N	\N	09010535807	2025-09-08 17:03:35
11	کاربر 5595	09013485595@mobile.pishkhanak.com	\N	$2y$12$oip.QyyFqlogdToXADXTEu7fbZQu9HrkfXSV2yIDWTk0IxAZscgNK	\N	2025-09-08 17:03:36	2025-09-08 17:03:36	\N	\N	\N	\N	09013485595	2025-09-08 17:03:35
12	کاربر 0723	09014340723@mobile.pishkhanak.com	\N	$2y$12$eJOopZX5S6PTRqxVDNZ9P.WLITDU67TCCX7wXL8rddXzbkyGU/jCK	\N	2025-09-08 17:03:36	2025-09-08 17:03:36	\N	\N	\N	\N	09014340723	2025-09-08 17:03:36
13	کاربر 2350	09024232350@mobile.pishkhanak.com	\N	$2y$12$pXpYmZg8./ni0thPVVvfYOyUTYD8AkqTNHFC8tPTGocXPkWps3NVe	\N	2025-09-08 17:03:36	2025-09-08 17:03:36	\N	\N	\N	\N	09024232350	2025-09-08 17:03:36
14	کاربر 0656	09026870656@mobile.pishkhanak.com	\N	$2y$12$kDBCrAA8X8ZpOfWpoujh4ODMAnT53dAth8YQmXkai9QdNQapQLdLe	\N	2025-09-08 17:03:36	2025-09-08 17:03:36	\N	\N	\N	\N	09026870656	2025-09-08 17:03:36
15	کاربر 1090	09028941090@mobile.pishkhanak.com	\N	$2y$12$lV72XjoJwXgq.cuMQ0.tuePQWI6jFOxIXUgCU09MpzQT.eKcxtNDy	\N	2025-09-08 17:03:37	2025-09-08 17:03:37	\N	\N	\N	\N	09028941090	2025-09-08 17:03:36
16	کاربر 1116	09031991116@mobile.pishkhanak.com	\N	$2y$12$MAtUKh6KJjxJ080eARy6le0J9n3h0rounvpQEU.7fITELK5YxTVBG	\N	2025-09-08 17:03:37	2025-09-08 17:03:37	\N	\N	\N	\N	09031991116	2025-09-08 17:03:37
17	کاربر 4039	09032114039@mobile.pishkhanak.com	\N	$2y$12$IQxSBttogT99h2ExaHUAVuyPgZhA6Aa14hPqzPqgxUGDUiQ7mmUdy	\N	2025-09-08 17:03:37	2025-09-08 17:03:37	\N	\N	\N	\N	09032114039	2025-09-08 17:03:37
18	کاربر 7235	09036427235@mobile.pishkhanak.com	\N	$2y$12$MF7k9BIumhLJ1hv/z.1fA.6rHk9uF00exahUlGhQBh1Zu1QGaLvuy	\N	2025-09-08 17:03:37	2025-09-08 17:03:37	\N	\N	\N	\N	09036427235	2025-09-08 17:03:37
19	کاربر 9250	09046139250@mobile.pishkhanak.com	\N	$2y$12$XASmqaIHm42DL5V.p63bBOR0ayW9Wd0pg1YUUcBfLsDjJBaZSvHQy	\N	2025-09-08 17:03:38	2025-09-08 17:03:38	\N	\N	\N	\N	09046139250	2025-09-08 17:03:37
20	کاربر 1970	09046911970@mobile.pishkhanak.com	\N	$2y$12$10QGYgMwjJilUR2xG5bmlOG7LmkRPagT30uj.nAZyxdcPih4lRWTO	\N	2025-09-08 17:03:38	2025-09-08 17:03:38	\N	\N	\N	\N	09046911970	2025-09-08 17:03:38
21	کاربر 2510	09050102510@mobile.pishkhanak.com	\N	$2y$12$VMgT1FycutpHs.LYIHqKoORufgWrY7YzyPoQ8xkuJh5xhUfylt.ay	\N	2025-09-08 17:03:38	2025-09-08 17:03:38	\N	\N	\N	\N	09050102510	2025-09-08 17:03:38
22	کاربر 3536	09051023536@mobile.pishkhanak.com	\N	$2y$12$YdzW4u3U..cOw/kW7ZhtwuLU01PxO4nEQ1H4awoGYDQ4mkunVPMT.	\N	2025-09-08 17:03:39	2025-09-08 17:03:39	\N	\N	\N	\N	09051023536	2025-09-08 17:03:38
23	کاربر 6579	09056026579@mobile.pishkhanak.com	\N	$2y$12$/isBIFWqfx.qK0NzIiGmK.moWXw4VpUGSruPBi2JRGTVnxZ32Byh2	\N	2025-09-08 17:03:39	2025-09-08 17:03:39	\N	\N	\N	\N	09056026579	2025-09-08 17:03:39
24	کاربر 7082	09100347082@mobile.pishkhanak.com	\N	$2y$12$CgrTD1LgKeVFlXo5NCnPY.uNBSvFvQkpZcAiCV7Z1kDN9/.JYmQuy	\N	2025-09-08 17:03:39	2025-09-08 17:03:39	\N	\N	\N	\N	09100347082	2025-09-08 17:03:39
25	کاربر 4001	09105684001@mobile.pishkhanak.com	\N	$2y$12$6FKDbk/h4OLYfAIC6fUCYOPCv7oQwKm.duFs0/McPzxrj0fE.RzjS	\N	2025-09-08 17:03:39	2025-09-08 17:03:39	\N	\N	\N	\N	09105684001	2025-09-08 17:03:39
26	کاربر 7092	09106427092@mobile.pishkhanak.com	\N	$2y$12$cSyBspD98/pgyISpr//oBeabq2huKUsqsK6NXyARSdBDyPbsqdNwe	\N	2025-09-08 17:03:40	2025-09-08 17:03:40	\N	\N	\N	\N	09106427092	2025-09-08 17:03:39
27	کاربر 6198	09111206198@mobile.pishkhanak.com	\N	$2y$12$oTIAQXAZE5v76FNauTjzyuyDhpdI8VcoeZWbg2I2pofXJ4jTE022O	\N	2025-09-08 17:03:40	2025-09-08 17:03:40	\N	\N	\N	\N	09111206198	2025-09-08 17:03:40
28	کاربر 6824	09112236824@mobile.pishkhanak.com	\N	$2y$12$7ppsCWFYiH6fEBmOraHLY..0cMBNBSyS27rat.9to6l0OsVWsx33y	\N	2025-09-08 17:03:40	2025-09-08 17:03:40	\N	\N	\N	\N	09112236824	2025-09-08 17:03:40
29	کاربر 8238	09112468238@mobile.pishkhanak.com	\N	$2y$12$bL9wEh0dFHX0EFoyTpkcie9KtTeFlM9ecCWBIM/th4EKZr8fwm7IS	\N	2025-09-08 17:03:40	2025-09-08 17:03:40	\N	\N	\N	\N	09112468238	2025-09-08 17:03:40
30	کاربر 9351	09112759351@mobile.pishkhanak.com	\N	$2y$12$ozwkLcq7OYSm3BgB0PVmTOfb99r37YF.h1ra.5DGX7Bm24jSYg5HK	\N	2025-09-08 17:03:41	2025-09-08 17:03:41	\N	\N	\N	\N	09112759351	2025-09-08 17:03:40
31	کاربر 0376	09115760376@mobile.pishkhanak.com	\N	$2y$12$RNyKOhYB6/eOlxFUa4iqdOcYOL62ZpkvRmQevkli7CZ02Kju3Ab8q	\N	2025-09-08 17:03:41	2025-09-08 17:03:41	\N	\N	\N	\N	09115760376	2025-09-08 17:03:41
32	کاربر 4437	09116204437@mobile.pishkhanak.com	\N	$2y$12$wBo/TeKhoQ1KXaD8bV8DeOVsCU1oSqSbXxLFS7DWxQDjATNMK3Hai	\N	2025-09-08 17:03:41	2025-09-08 17:03:41	\N	\N	\N	\N	09116204437	2025-09-08 17:03:41
33	کاربر 6933	09120216933@mobile.pishkhanak.com	\N	$2y$12$mBr/6/uBK3jtdUjhTFFlo.wSyBA5juLq3Vy2mfGW7iXKDGX..xzRO	\N	2025-09-08 17:03:42	2025-09-08 17:03:42	\N	\N	\N	\N	09120216933	2025-09-08 17:03:41
34	کاربر 0479	09120560479@mobile.pishkhanak.com	\N	$2y$12$bTP8mk6PHvT3JjdEKa8S8uFVhOecSWM2LakYwJ2XXRyPFfKw0D19m	\N	2025-09-08 17:03:42	2025-09-08 17:03:42	\N	\N	\N	\N	09120560479	2025-09-08 17:03:42
36	کاربر 4566	09120764566@mobile.pishkhanak.com	\N	$2y$12$LzMCb/L.cGZ9Cx/OBv2DXO3dT9m0cpfw9RtvSDIp3MBAwKMo0h9/K	\N	2025-09-08 17:03:42	2025-09-08 17:03:42	\N	\N	\N	\N	09120764566	2025-09-08 17:03:42
37	کاربر 5265	09120895265@mobile.pishkhanak.com	\N	$2y$12$t8nuyrshB.6Xv2fMQArCn.SN99QmUe/A.HQKDwdugxUi.pnT.UGTO	\N	2025-09-08 17:03:43	2025-09-08 17:03:43	\N	\N	\N	\N	09120895265	2025-09-08 17:03:42
38	کاربر 3858	09120943858@mobile.pishkhanak.com	\N	$2y$12$xGLZJCUhl.rbzqfad5D6pu1f6kkz7FbK6UxHpgxagGpZafqTMb51a	\N	2025-09-08 17:03:43	2025-09-08 17:03:43	\N	\N	\N	\N	09120943858	2025-09-08 17:03:43
40	کاربر 8906	09121238906@mobile.pishkhanak.com	\N	$2y$12$yKk8zC3vbZueXAPxN0F0x.ISOlFdbTRuUaZwxMPQbVf8swwEGjE2q	\N	2025-09-08 17:03:43	2025-09-08 17:03:43	\N	\N	\N	\N	09121238906	2025-09-08 17:03:43
41	کاربر 3906	09121893906@mobile.pishkhanak.com	\N	$2y$12$ZisSPBGU7pwQ51Gv8r.PfufLhprZK2HNtPKcqtGN1FZ6cdZeZA77e	\N	2025-09-08 17:03:44	2025-09-08 17:03:44	\N	\N	\N	\N	09121893906	2025-09-08 17:03:43
42	کاربر 9251	09122089251@mobile.pishkhanak.com	\N	$2y$12$gp3XYOL3tr1aSf7dmwiRfOHfiwAX1TBmxB5hU1UtDvbLBrcu.yCJK	\N	2025-09-08 17:03:44	2025-09-08 17:03:44	\N	\N	\N	\N	09122089251	2025-09-08 17:03:44
43	کاربر 0031	09122220031@mobile.pishkhanak.com	\N	$2y$12$7y3qKM3RaveMxHX3c87jHOCE9m3o5r1DGQt9..Uqfp6uwqV2fsEna	\N	2025-09-08 17:03:44	2025-09-08 17:03:44	\N	\N	\N	\N	09122220031	2025-09-08 17:03:44
44	کاربر 1697	09122281697@mobile.pishkhanak.com	\N	$2y$12$RrGsBJNNxxgtLpvUqGsK1uqkymvScZPngYWeyPxPUFDUoxnjiKCPq	\N	2025-09-08 17:03:45	2025-09-08 17:03:45	\N	\N	\N	\N	09122281697	2025-09-08 17:03:44
45	کاربر 3690	09122603690@mobile.pishkhanak.com	\N	$2y$12$x/t6kPrsqeaV8AOYkipPY.q6R9KtBgC8btzxgkkjfUWYjT1.lxAAm	\N	2025-09-08 17:03:45	2025-09-08 17:03:45	\N	\N	\N	\N	09122603690	2025-09-08 17:03:45
46	کاربر 2105	09122782105@mobile.pishkhanak.com	\N	$2y$12$dcHnXwruSW2098usSBJo/OMGD3xwiDpb7vPyNNSZX2vRpkHz.egDq	\N	2025-09-08 17:03:45	2025-09-08 17:03:45	\N	\N	\N	\N	09122782105	2025-09-08 17:03:45
47	کاربر 7596	09122857596@mobile.pishkhanak.com	\N	$2y$12$zj/0M55usSaFh11Tugjwq.LtYKAJeuB0osk6hTKu9Fmzb0QvbkIt6	\N	2025-09-08 17:03:45	2025-09-08 17:03:45	\N	\N	\N	\N	09122857596	2025-09-08 17:03:45
48	کاربر 3877	09123573877@mobile.pishkhanak.com	\N	$2y$12$2CanKnIot.H9X0UpaqlsfekO0RHNayDpDbJJuXDtAdTX6M5MYxB6.	\N	2025-09-08 17:03:46	2025-09-08 17:03:46	\N	\N	\N	\N	09123573877	2025-09-08 17:03:45
49	کاربر 3135	09124063135@mobile.pishkhanak.com	\N	$2y$12$pcdOlxsXizcJY21cCjI0Mubxz9w.Buy4JGv0W.1yTXA0vOVv7pC8y	\N	2025-09-08 17:03:46	2025-09-08 17:03:46	\N	\N	\N	\N	09124063135	2025-09-08 17:03:46
51	کاربر 3508	09124853508@mobile.pishkhanak.com	\N	$2y$12$CGnVpsi81f8o/oVmCXyzX.tt7Imp3EDMRIIKb.fp5FBu8fBvDbKIm	\N	2025-09-08 17:03:46	2025-09-08 17:03:46	\N	\N	\N	\N	09124853508	2025-09-08 17:03:46
52	کاربر 7463	09125187463@mobile.pishkhanak.com	\N	$2y$12$gRQT1yhtfNZGliOuynAYzuArVDw4YPPIaCvcNVIbMi8SsYqEkl3RW	\N	2025-09-08 17:03:47	2025-09-08 17:03:47	\N	\N	\N	\N	09125187463	2025-09-08 17:03:46
53	کاربر 0845	09125880845@mobile.pishkhanak.com	\N	$2y$12$bus8nNejfzoaPT9JvieZgutoVnlI3p0HNaAqUsZzjp2qpTPPu2mFS	\N	2025-09-08 17:03:47	2025-09-08 17:03:47	\N	\N	\N	\N	09125880845	2025-09-08 17:03:47
54	کاربر 3453	09126463453@mobile.pishkhanak.com	\N	$2y$12$vKfCfveQeGic/o.XTem5feiSwNNXWVbIGaJx6fIvv4gq/gc.8wBNO	\N	2025-09-08 17:03:47	2025-09-08 17:03:47	\N	\N	\N	\N	09126463453	2025-09-08 17:03:47
55	کاربر 4642	09126494642@mobile.pishkhanak.com	\N	$2y$12$GKPMIx0nLqp4s0CvhtO3COiacNa3p3PXKK2eMz/ZFE5bCiwFoLI8a	\N	2025-09-08 17:03:48	2025-09-08 17:03:48	\N	\N	\N	\N	09126494642	2025-09-08 17:03:47
56	کاربر 6823	09127036823@mobile.pishkhanak.com	\N	$2y$12$FvUCWTsj9uCvgxYCHnIXWuEvaFMrP/HRgtxxs.wAGVvRhrz6mD9Pm	\N	2025-09-08 17:03:48	2025-09-08 17:03:48	\N	\N	\N	\N	09127036823	2025-09-08 17:03:48
57	کاربر 4220	09127154220@mobile.pishkhanak.com	\N	$2y$12$uRYelmdipIOoH5E0RbntaOYD61homhq84ruXnpSCa.cosCCi2eI62	\N	2025-09-08 17:03:48	2025-09-08 17:03:48	\N	\N	\N	\N	09127154220	2025-09-08 17:03:48
58	کاربر 8843	09127878843@mobile.pishkhanak.com	\N	$2y$12$4erkuAZE9mQA8RKvmfVhFuZfmDc3N3gz9UHOb1eASMUmzmSNr73KO	\N	2025-09-08 17:03:48	2025-09-08 17:03:48	\N	\N	\N	\N	09127878843	2025-09-08 17:03:48
59	کاربر 9781	09128389781@mobile.pishkhanak.com	\N	$2y$12$KGpHJ5W11guYE2smKwe9nei2OqrQqCkAQHy/teE.Ep6XOaL9w0yDy	\N	2025-09-08 17:03:49	2025-09-08 17:03:49	\N	\N	\N	\N	09128389781	2025-09-08 17:03:48
60	کاربر 3039	09128713039@mobile.pishkhanak.com	\N	$2y$12$ioC/LWD/tZngaOXAC4GqmutW2v1fRwz02YX6geaExuQNq7bKIZGJ.	\N	2025-09-08 17:03:49	2025-09-08 17:03:49	\N	\N	\N	\N	09128713039	2025-09-08 17:03:49
61	کاربر 8869	09128898869@mobile.pishkhanak.com	\N	$2y$12$bLEuet5I3T1oCw4S7sydouzKYmHb8i06JpdgdV3sK6CGY4zq.vAz6	\N	2025-09-08 17:03:49	2025-09-08 17:03:49	\N	\N	\N	\N	09128898869	2025-09-08 17:03:49
62	کاربر 9711	09129279711@mobile.pishkhanak.com	\N	$2y$12$RLOPvf5MJ3mCxBxYlkTuIe8xkhAJFd/.l1PhSlZpwFZKHp/hIKWPO	\N	2025-09-08 17:03:49	2025-09-08 17:03:49	\N	\N	\N	\N	09129279711	2025-09-08 17:03:49
63	کاربر 6625	09129646625@mobile.pishkhanak.com	\N	$2y$12$VMzMHhhgygLQ1M34EauvaeInZEQTfjoPrrtmJ6PnlZiM.P0bKqmSC	\N	2025-09-08 17:03:50	2025-09-08 17:03:50	\N	\N	\N	\N	09129646625	2025-09-08 17:03:49
64	کاربر 5238	09129675238@mobile.pishkhanak.com	\N	$2y$12$148s5BmJcO.jjF1GOOeggedxqGPuT.gMHQK7sr3KEYasp1Gjuoc/m	\N	2025-09-08 17:03:50	2025-09-08 17:03:50	\N	\N	\N	\N	09129675238	2025-09-08 17:03:50
65	کاربر 3325	09130683325@mobile.pishkhanak.com	\N	$2y$12$1PU7qzVb49cwBuFcHm8wf.jdOdfIEbGUeKQ4JNgkeNYoDu1tlQRK6	\N	2025-09-08 17:03:50	2025-09-08 17:03:50	\N	\N	\N	\N	09130683325	2025-09-08 17:03:50
66	کاربر 9414	09131099414@mobile.pishkhanak.com	\N	$2y$12$.nzgU7pUsidpt7whdOoqF.2TRAUQFG26SnahYS9efRYXkaaHqHh4q	\N	2025-09-08 17:03:50	2025-09-08 17:03:50	\N	\N	\N	\N	09131099414	2025-09-08 17:03:50
67	کاربر 8144	09131588144@mobile.pishkhanak.com	\N	$2y$12$WuGySKUTqMl5V/IgM3Jnpe6.pU9zRjmcbtLM29Wzl4yMbw0x821w.	\N	2025-09-08 17:03:51	2025-09-08 17:03:51	\N	\N	\N	\N	09131588144	2025-09-08 17:03:50
68	کاربر 0211	09132070211@mobile.pishkhanak.com	\N	$2y$12$Db8Y1hMhLTyTZv/rxAMucOIOursApTmEd3D.nXAhjZpynySu/hwaK	\N	2025-09-08 17:03:51	2025-09-08 17:03:51	\N	\N	\N	\N	09132070211	2025-09-08 17:03:51
69	کاربر 6744	09132556744@mobile.pishkhanak.com	\N	$2y$12$JEYvbNVEG//nKaCt5J5N1OzR.OLHOV2Uz0oghCAgRwLfmKs2siXhK	\N	2025-09-08 17:03:51	2025-09-08 17:03:51	\N	\N	\N	\N	09132556744	2025-09-08 17:03:51
70	کاربر 8823	09133998823@mobile.pishkhanak.com	\N	$2y$12$5SHX8sjpVdHIgocZQTr.I.uQjAnBf/oVldy8uJgDAXPp8BU87Bufi	\N	2025-09-08 17:03:51	2025-09-08 17:03:51	\N	\N	\N	\N	09133998823	2025-09-08 17:03:51
71	کاربر 6952	09134636952@mobile.pishkhanak.com	\N	$2y$12$wj8m2hxBZLApYhgmw1BUo./YHR1jLYgzpCvnRFiXd2n8cUYNJR1ni	\N	2025-09-08 17:03:52	2025-09-08 17:03:52	\N	\N	\N	\N	09134636952	2025-09-08 17:03:51
72	کاربر 9971	09134829971@mobile.pishkhanak.com	\N	$2y$12$avFHpw4BWErHHD802WWdSuVaG0f0sZV/God9Z/RbJ22keeEQEUcrq	\N	2025-09-08 17:03:52	2025-09-08 17:03:52	\N	\N	\N	\N	09134829971	2025-09-08 17:03:52
73	کاربر 9110	09137829110@mobile.pishkhanak.com	\N	$2y$12$vnPtybFasKH1O0OhFZwLkOPLpAiWjhoELk6vXTZ7q/8tuAoWagXIa	\N	2025-09-08 17:03:52	2025-09-08 17:03:52	\N	\N	\N	\N	09137829110	2025-09-08 17:03:52
74	کاربر 1734	09138521734@mobile.pishkhanak.com	\N	$2y$12$5g650LmbgreGSMm.u5hpfuWPLLw3/.4h1eyPL60u7tzI.cAMmzLny	\N	2025-09-08 17:03:53	2025-09-08 17:03:53	\N	\N	\N	\N	09138521734	2025-09-08 17:03:52
75	کاربر 5909	09140075909@mobile.pishkhanak.com	\N	$2y$12$GbojnxxuBe9pmmz0Wd.SA.1EulTpo7Dk4kKYth592ovFJP5A7yxMC	\N	2025-09-08 17:03:53	2025-09-08 17:03:53	\N	\N	\N	\N	09140075909	2025-09-08 17:03:53
76	کاربر 8932	09141488932@mobile.pishkhanak.com	\N	$2y$12$Gp05BoLTtanWEMPfG1pSb.Y3fnhbi1j49EEcknHePI641PJDTDjy6	\N	2025-09-08 17:03:53	2025-09-08 17:03:53	\N	\N	\N	\N	09141488932	2025-09-08 17:03:53
77	کاربر 2960	09142402960@mobile.pishkhanak.com	\N	$2y$12$LFasaAPCr7urSy/tcmW8RukA4d0M1Ef3xV22o2JgmjYm77bYKTzTK	\N	2025-09-08 17:03:53	2025-09-08 17:03:53	\N	\N	\N	\N	09142402960	2025-09-08 17:03:53
78	کاربر 6933	09143156933@mobile.pishkhanak.com	\N	$2y$12$raISWpHO7KS8oxkkQQ.J8eB4zsqlrTQFflZnSyfhoR5UqBZJaQG1e	\N	2025-09-08 17:03:54	2025-09-08 17:03:54	\N	\N	\N	\N	09143156933	2025-09-08 17:03:53
79	کاربر 9471	09143579471@mobile.pishkhanak.com	\N	$2y$12$RCzDD7uzybLofd70LdjEd.SQpyZIysos32GsjUzAmLtCwUsroWUsi	\N	2025-09-08 17:03:54	2025-09-08 17:03:54	\N	\N	\N	\N	09143579471	2025-09-08 17:03:54
80	کاربر 8221	09144108221@mobile.pishkhanak.com	\N	$2y$12$xSIUFHXNPy21wek7mKvdpeOOm6oTbwLVgdRH/KB4b1F3fFaZJ8UEy	\N	2025-09-08 17:03:54	2025-09-08 17:03:54	\N	\N	\N	\N	09144108221	2025-09-08 17:03:54
81	کاربر 2079	09149042079@mobile.pishkhanak.com	\N	$2y$12$iKZQJGJSzT.kNJ1P7pGAn.PemTM2octOMlw/ayJu0y8mkAog/FrJG	\N	2025-09-08 17:03:54	2025-09-08 17:03:54	\N	\N	\N	\N	09149042079	2025-09-08 17:03:54
82	کاربر 3076	09149263076@mobile.pishkhanak.com	\N	$2y$12$XPZvDS9XoDY7Vsv.45nRIefPGEX.bbMg9wSzfqbwW/NJKilK9zAGW	\N	2025-09-08 17:03:55	2025-09-08 17:03:55	\N	\N	\N	\N	09149263076	2025-09-08 17:03:54
83	کاربر 2550	09153332550@mobile.pishkhanak.com	\N	$2y$12$etHzMHqMSqMAamgzVFv7O.1f2MZhMIMUyNTM..d66fDYFCeXOZ40G	\N	2025-09-08 17:03:55	2025-09-08 17:03:55	\N	\N	\N	\N	09153332550	2025-09-08 17:03:55
84	کاربر 7148	09153337148@mobile.pishkhanak.com	\N	$2y$12$u6HysOLpnFL1CaZ6vJ8Ey.sVr4kbcCaKMmC3Ga3nFR.x9buMpnRC.	\N	2025-09-08 17:03:55	2025-09-08 17:03:55	\N	\N	\N	\N	09153337148	2025-09-08 17:03:55
85	کاربر 0603	09160960603@mobile.pishkhanak.com	\N	$2y$12$l/wOPvh5TqUcROsxr3SNO.lmbMwKEl4EHH3mwhAeGyOz/QYvmtOw.	\N	2025-09-08 17:03:55	2025-09-08 17:03:55	\N	\N	\N	\N	09160960603	2025-09-08 17:03:55
86	کاربر 0021	09162640021@mobile.pishkhanak.com	\N	$2y$12$9XhgT/hfVduCwfI2vtawguBc6M0ZqUsElZXyoBr.OybsNwB2TNwdK	\N	2025-09-08 17:03:56	2025-09-08 17:03:56	\N	\N	\N	\N	09162640021	2025-09-08 17:03:55
87	کاربر 5739	09163415739@mobile.pishkhanak.com	\N	$2y$12$3n1PHjUygzEa1XG9t53WX.IJ8SCL8Bw1oDQn/XSKUFd61cMiv.deK	\N	2025-09-08 17:03:56	2025-09-08 17:03:56	\N	\N	\N	\N	09163415739	2025-09-08 17:03:56
88	کاربر 2127	09163472127@mobile.pishkhanak.com	\N	$2y$12$9S6OyeXqkRBaMF533xDuaetG7yAk00guSrYBEaI7iNVKazwumo.VS	\N	2025-09-08 17:03:56	2025-09-08 17:03:56	\N	\N	\N	\N	09163472127	2025-09-08 17:03:56
89	کاربر 6760	09166986760@mobile.pishkhanak.com	\N	$2y$12$ZC23TnkvCfgZt7VD1NChre6TY6YB/QxIfNFUIBUOxz08jjy./WP9y	\N	2025-09-08 17:03:56	2025-09-08 17:03:56	\N	\N	\N	\N	09166986760	2025-09-08 17:03:56
91	کاربر 6838	09171016838@mobile.pishkhanak.com	\N	$2y$12$iYQAoVqtC5ZVVUQQO1xMQ.cy.nrzLsK8cc2X1SwA7iJMOTPfE2LCO	\N	2025-09-08 17:03:57	2025-09-08 17:03:57	\N	\N	\N	\N	09171016838	2025-09-08 17:03:57
92	کاربر 2605	09171482605@mobile.pishkhanak.com	\N	$2y$12$/5z4movhQLGBYlUCjZUEvung1Ce979UC67FDjF5dZbEqBLy7TBlvO	\N	2025-09-08 17:03:57	2025-09-08 17:03:57	\N	\N	\N	\N	09171482605	2025-09-08 17:03:57
93	کاربر 2733	09173842733@mobile.pishkhanak.com	\N	$2y$12$HaqxxkyNU1PG8nyw.GR2y.0pl/QtGcoEfrR8pT3.k.mEaw.4Ta/pC	\N	2025-09-08 17:03:58	2025-09-08 17:03:58	\N	\N	\N	\N	09173842733	2025-09-08 17:03:57
94	کاربر 2369	09177162369@mobile.pishkhanak.com	\N	$2y$12$sKV8az75ogk/kPDc265VQeSUdI4OkeyH9vSgRGU7Gh39TfHdnpwO.	\N	2025-09-08 17:03:58	2025-09-08 17:03:58	\N	\N	\N	\N	09177162369	2025-09-08 17:03:58
95	کاربر 5319	09178355319@mobile.pishkhanak.com	\N	$2y$12$.qopf7SenG4068u6eAIsKuOfN325z9PvLJdz1xQa79RmQUQWoC62i	\N	2025-09-08 17:03:58	2025-09-08 17:03:58	\N	\N	\N	\N	09178355319	2025-09-08 17:03:58
96	کاربر 8608	09179228608@mobile.pishkhanak.com	\N	$2y$12$aBnmfa5lGkJ/ngB93kUTeOJ5d.oFuRE/kApMO6EwUuGtRT71gqRMK	\N	2025-09-08 17:03:58	2025-09-08 17:03:58	\N	\N	\N	\N	09179228608	2025-09-08 17:03:58
97	کاربر 5290	09183365290@mobile.pishkhanak.com	\N	$2y$12$TcZfH2WIhVIIZCmfMY/lwOS38fQyEyZni/McdRzdJLvyQc3nwEH36	\N	2025-09-08 17:03:59	2025-09-08 17:03:59	\N	\N	\N	\N	09183365290	2025-09-08 17:03:58
98	کاربر 9193	09183759193@mobile.pishkhanak.com	\N	$2y$12$1NhFdJlETaZoBhFPtBuNaOGwlgpS1O7gMuPvhrRR26bUtjZYzXB92	\N	2025-09-08 17:03:59	2025-09-08 17:03:59	\N	\N	\N	\N	09183759193	2025-09-08 17:03:59
99	کاربر 8022	09187548022@mobile.pishkhanak.com	\N	$2y$12$55tdowrob5SrjnnHcLb67uh0CmQKR1Yv7MvsAtNBeyPygKVX8axVW	\N	2025-09-08 17:03:59	2025-09-08 17:03:59	\N	\N	\N	\N	09187548022	2025-09-08 17:03:59
100	کاربر 4737	09188724737@mobile.pishkhanak.com	\N	$2y$12$uVIP507AKEcYlSMw/I9jBOxZCsF8EJ5ICEV.ddQx9tDrpwQdoNszi	\N	2025-09-08 17:03:59	2025-09-08 17:03:59	\N	\N	\N	\N	09188724737	2025-09-08 17:03:59
101	کاربر 9433	09188729433@mobile.pishkhanak.com	\N	$2y$12$kimTICRlQK6HbFBZzDI62.3k2C80lFdQ0cjlT786o1Jj5iUOyCYPK	\N	2025-09-08 17:04:00	2025-09-08 17:04:00	\N	\N	\N	\N	09188729433	2025-09-08 17:03:59
102	کاربر 9203	09188769203@mobile.pishkhanak.com	\N	$2y$12$9ZaGA86.BpeMcUJl4NFib.1HwAsqByeSCttfRLQzLKh4tvdPx7sv.	\N	2025-09-08 17:04:00	2025-09-08 17:04:00	\N	\N	\N	\N	09188769203	2025-09-08 17:04:00
103	کاربر 7656	09189927656@mobile.pishkhanak.com	\N	$2y$12$iQREvqQZS2Xrz7xnzxVM9Onq0UVPaNwOHAfS5Ith7JhSjzuHEaCca	\N	2025-09-08 17:04:00	2025-09-08 17:04:00	\N	\N	\N	\N	09189927656	2025-09-08 17:04:00
104	کاربر 5553	09190355553@mobile.pishkhanak.com	\N	$2y$12$ezrFCP4oM1LdZTc7FYH1sOAdLGrLaDKk3Ap5SCT5RHlVkbGMOVarC	\N	2025-09-08 17:04:00	2025-09-08 17:04:00	\N	\N	\N	\N	09190355553	2025-09-08 17:04:00
105	کاربر 0027	09193350027@mobile.pishkhanak.com	\N	$2y$12$AplaJhoJiJ2kLdlJIyEQK.QqeoiKXo7zBYK/HUiKIeTPFoEEKXLB2	\N	2025-09-08 17:04:01	2025-09-08 17:04:01	\N	\N	\N	\N	09193350027	2025-09-08 17:04:00
106	کاربر 2883	09193372883@mobile.pishkhanak.com	\N	$2y$12$ZqLeEb.MbVZ/kvfZneY0o.JVVr5P0DpF9Ww0RjlGayPImsOEd3ntO	\N	2025-09-08 17:04:01	2025-09-08 17:04:01	\N	\N	\N	\N	09193372883	2025-09-08 17:04:01
107	کاربر 8865	09193688865@mobile.pishkhanak.com	\N	$2y$12$ydzdSc7oqui3zOGCtd6CMufKLBVb4NvyC4QwPp48wDX8QQfNyWCPW	\N	2025-09-08 17:04:02	2025-09-08 17:04:02	\N	\N	\N	\N	09193688865	2025-09-08 17:04:01
108	کاربر 7185	09193777185@mobile.pishkhanak.com	\N	$2y$12$miuiIxKD9JzTBtduCH52sOgx/NfTWc/mALLhxugKCZV407WkU8hne	\N	2025-09-08 17:04:02	2025-09-08 17:04:02	\N	\N	\N	\N	09193777185	2025-09-08 17:04:02
110	کاربر 1306	09194761306@mobile.pishkhanak.com	\N	$2y$12$/u5XkskwSkw7cmlB5JaPuujVMZS.7z2vZq5RO4bBCdGAMR6jZNrn2	\N	2025-09-08 17:04:03	2025-09-08 17:04:03	\N	\N	\N	\N	09194761306	2025-09-08 17:04:02
111	کاربر 1088	09194961088@mobile.pishkhanak.com	\N	$2y$12$7gHxXw/ey.eO9bZSgpR3oOozeI/fTTxzPP78YE4MvnK8vXvVTdZcK	\N	2025-09-08 17:04:03	2025-09-08 17:04:03	\N	\N	\N	\N	09194961088	2025-09-08 17:04:03
112	کاربر 5822	09195885822@mobile.pishkhanak.com	\N	$2y$12$Zgsk6WpP/vOaB9hbJDCxnuUAEBnZX9IkKVbcFRgYWxw0DeaZFPSXS	\N	2025-09-08 17:04:03	2025-09-08 17:04:03	\N	\N	\N	\N	09195885822	2025-09-08 17:04:03
114	کاربر 8450	09197928450@mobile.pishkhanak.com	\N	$2y$12$KmH9WstxNJH.GbQhc2m8mOIK8.o4nko6eGp8/pH//MZnZKiwgreQK	\N	2025-09-08 17:04:04	2025-09-08 17:04:04	\N	\N	\N	\N	09197928450	2025-09-08 17:04:03
115	کاربر 6456	09198856456@mobile.pishkhanak.com	\N	$2y$12$qFSdpIVQwaAAN9JUZOXyOuc.YE9hmnOpHGHHtLJk1yse/TG1g/21e	\N	2025-09-08 17:04:04	2025-09-08 17:04:04	\N	\N	\N	\N	09198856456	2025-09-08 17:04:04
116	کاربر 5739	09199915739@mobile.pishkhanak.com	\N	$2y$12$urx81ZL26qV9t3QuCuWXL.rSZKxyZIipE/aDwBwZinIoszdol9MF2	\N	2025-09-08 17:04:04	2025-09-08 17:04:04	\N	\N	\N	\N	09199915739	2025-09-08 17:04:04
117	کاربر 9176	09212029176@mobile.pishkhanak.com	\N	$2y$12$KMUavhKj.qGOQ/P9CwGRYe8ekwV9zzx1oIOl7GyKX2/uWlv/sK7zG	\N	2025-09-08 17:04:04	2025-09-08 17:04:04	\N	\N	\N	\N	09212029176	2025-09-08 17:04:04
118	کاربر 3538	09216323538@mobile.pishkhanak.com	\N	$2y$12$iQaxniAqNtTtMmTKz5qSnuBwowPJWaK7vq8HKuf8Gq4OQ3NI1CYja	\N	2025-09-08 17:04:05	2025-09-08 17:04:05	\N	\N	\N	\N	09216323538	2025-09-08 17:04:04
119	کاربر 8562	09216798562@mobile.pishkhanak.com	\N	$2y$12$e1fHODavSQMeKmbwWtrY/.wknbzuF4WDJW3AXYynwGtFwQBOQd2Ue	\N	2025-09-08 17:04:05	2025-09-08 17:04:05	\N	\N	\N	\N	09216798562	2025-09-08 17:04:05
120	کاربر 9806	09217549806@mobile.pishkhanak.com	\N	$2y$12$5k.hXRRUyvUy.xZGC1Er1uIekoeWfIGVn3otclLjgiSBGxRBBiEyu	\N	2025-09-08 17:04:05	2025-09-08 17:04:05	\N	\N	\N	\N	09217549806	2025-09-08 17:04:05
121	کاربر 4716	09219894716@mobile.pishkhanak.com	\N	$2y$12$9IEwuD6s5ZwhurKZCn0p.uhq6wQHk9JaxkfVKatmNaKINmsOdKlyO	\N	2025-09-08 17:04:06	2025-09-08 17:04:06	\N	\N	\N	\N	09219894716	2025-09-08 17:04:05
122	کاربر 4028	09222864028@mobile.pishkhanak.com	\N	$2y$12$evrbTqE4aRHM1OOl20759eEQ6x.VEhB12zI3xpDi0QLHtxAyfz08O	\N	2025-09-08 17:04:06	2025-09-08 17:04:06	\N	\N	\N	\N	09222864028	2025-09-08 17:04:06
123	کاربر 8764	09225258764@mobile.pishkhanak.com	\N	$2y$12$Ec.SOjg1sSlEmaAqMpvz8OdUmNMhtB.AMEugLAoDmlL19onrLfOea	\N	2025-09-08 17:04:06	2025-09-08 17:04:06	\N	\N	\N	\N	09225258764	2025-09-08 17:04:06
109	کاربر 1183	09194551183@mobile.pishkhanak.com	\N	$2y$12$Nq4zOn2BhO9rFS/fT5Z9.eZMKxHE.q0nqcuZDM99gY0MQVAg30IQW	gUR0lyNp9MhYnSJtd7ju1u1bY91gyFyWkO63ddPC4eiThd76bXLr9CJPJe67	2025-09-08 17:04:02	2025-09-08 17:04:02	\N	\N	\N	\N	09194551183	2025-09-08 17:04:02
124	کاربر 1768	09228331768@mobile.pishkhanak.com	\N	$2y$12$fsKbNU5.Pk2EMOcC2FYE7erRg0OBG9ZFsgJsRn5MORpRoEYFMhxp2	\N	2025-09-08 17:04:06	2025-09-08 17:04:06	\N	\N	\N	\N	09228331768	2025-09-08 17:04:06
125	کاربر 6037	09306836037@mobile.pishkhanak.com	\N	$2y$12$qwuJYsMpd4PBZ4HYmBhB2.F0.ChPdHzg89kv.FtwP7fvDC7hiTpaC	\N	2025-09-08 17:04:07	2025-09-08 17:04:07	\N	\N	\N	\N	09306836037	2025-09-08 17:04:06
126	کاربر 2424	09309502424@mobile.pishkhanak.com	\N	$2y$12$aQLi513Jsh2SzB/l.QynnOQkL1l2KY9CG6Ey9B7rl5n7ZT0LiisfK	\N	2025-09-08 17:04:07	2025-09-08 17:04:07	\N	\N	\N	\N	09309502424	2025-09-08 17:04:07
127	کاربر 2446	09309742446@mobile.pishkhanak.com	\N	$2y$12$mQgNIUq3eXDOO79GI4B1qOdYUW/73/lOiVlc7m2OJwMPqN.rsyoQm	\N	2025-09-08 17:04:07	2025-09-08 17:04:07	\N	\N	\N	\N	09309742446	2025-09-08 17:04:07
128	کاربر 1591	09333931591@mobile.pishkhanak.com	\N	$2y$12$sJ1LRWsIJAnDJG0mjT9WDO430aFs47dbruXEaw4bnRVxO9PGO3s1.	\N	2025-09-08 17:04:07	2025-09-08 17:04:07	\N	\N	\N	\N	09333931591	2025-09-08 17:04:07
129	کاربر 4457	09338534457@mobile.pishkhanak.com	\N	$2y$12$oSGavFp5LcQG7XWrDiPN4.4R1nlsXJferkDEafl1Wnp5aSYUnIUba	\N	2025-09-08 17:04:08	2025-09-08 17:04:08	\N	\N	\N	\N	09338534457	2025-09-08 17:04:07
130	کاربر 3086	09361753086@mobile.pishkhanak.com	\N	$2y$12$IYU6x53PYu2zB8VpQGf2NOkQwhbf/BFToisra0Vy7t/IqQ8nptzMW	\N	2025-09-08 17:04:08	2025-09-08 17:04:08	\N	\N	\N	\N	09361753086	2025-09-08 17:04:08
131	کاربر 2493	09362062493@mobile.pishkhanak.com	\N	$2y$12$arF0JUhlQmKFK5UkCLSJvOx3sBtXeDGGJtpBadRQjF9CknV4GTJXu	\N	2025-09-08 17:04:08	2025-09-08 17:04:08	\N	\N	\N	\N	09362062493	2025-09-08 17:04:08
132	کاربر 7221	09362457221@mobile.pishkhanak.com	\N	$2y$12$39Jh6fCiPJkVzuBR.tZEauW45XLXcIC5WOfHeb/FgpL4Eb8szqSCC	\N	2025-09-08 17:04:08	2025-09-08 17:04:08	\N	\N	\N	\N	09362457221	2025-09-08 17:04:08
133	کاربر 7313	09363207313@mobile.pishkhanak.com	\N	$2y$12$2GFjQ/WPaoqXofe7QdzCAO9HIRFEQqn53RAZJp/CQ60OC/16Hu7pK	\N	2025-09-08 17:04:09	2025-09-08 17:04:09	\N	\N	\N	\N	09363207313	2025-09-08 17:04:09
134	کاربر 2772	09364232772@mobile.pishkhanak.com	\N	$2y$12$Ad/T7ew8Et.FmzFpye499u025P4Wo1gFn2Rh7QwY8XScgoJKCMHhS	\N	2025-09-08 17:04:09	2025-09-08 17:04:09	\N	\N	\N	\N	09364232772	2025-09-08 17:04:09
135	کاربر 5055	09364285055@mobile.pishkhanak.com	\N	$2y$12$ozRSTqhVjUQ1FFc/akQVp.eJoCFOLbP.xJjo9tWC1BDbiLxPiAcYa	\N	2025-09-08 17:04:09	2025-09-08 17:04:09	\N	\N	\N	\N	09364285055	2025-09-08 17:04:09
136	کاربر 1910	09365131910@mobile.pishkhanak.com	\N	$2y$12$WdVQ4Icu8xTFaqozQXfNyOk4zXh8T8qW4cgKEw8B6yUiFUAKJoXMW	\N	2025-09-08 17:04:10	2025-09-08 17:04:10	\N	\N	\N	\N	09365131910	2025-09-08 17:04:09
137	کاربر 9432	09367009432@mobile.pishkhanak.com	\N	$2y$12$if31vSdyWl4QvLyboKGtmO1aiEXSCszjZgJMN4ZGPTEctQ7AGabui	\N	2025-09-08 17:04:10	2025-09-08 17:04:10	\N	\N	\N	\N	09367009432	2025-09-08 17:04:10
138	کاربر 5219	09368735219@mobile.pishkhanak.com	\N	$2y$12$deo5cjLNzrcpJD/5OKeePO/IBCFlBTjbqm6Et.p7XUvPMURAarx/2	\N	2025-09-08 17:04:10	2025-09-08 17:04:10	\N	\N	\N	\N	09368735219	2025-09-08 17:04:10
139	کاربر 8662	09369508662@mobile.pishkhanak.com	\N	$2y$12$dFFjQC3IBas5iKH8GGwsaueLNhIXODWcqgbqUNnbEkV8fbIsLmP5G	\N	2025-09-08 17:04:10	2025-09-08 17:04:10	\N	\N	\N	\N	09369508662	2025-09-08 17:04:10
140	کاربر 8518	09370168518@mobile.pishkhanak.com	\N	$2y$12$dpbXyA/qkueD.MY65KFuuOz6GnuFJzuiQWQtiYE0tVGpBdSDNxZaW	\N	2025-09-08 17:04:11	2025-09-08 17:04:11	\N	\N	\N	\N	09370168518	2025-09-08 17:04:10
141	کاربر 8008	09371088008@mobile.pishkhanak.com	\N	$2y$12$n2Np6vPxZGF.7eP3yibSb.aWa2ZxqtVz/rqOwtkS3FNipSOsw9nEK	\N	2025-09-08 17:04:11	2025-09-08 17:04:11	\N	\N	\N	\N	09371088008	2025-09-08 17:04:11
142	کاربر 3405	09377373405@mobile.pishkhanak.com	\N	$2y$12$4tgbty/WK447a9CUmKbYeOTY/eQ7jeuSMmA5rUx1AnAfVUezYVqpa	\N	2025-09-08 17:04:11	2025-09-08 17:04:11	\N	\N	\N	\N	09377373405	2025-09-08 17:04:11
143	کاربر 3625	09377373625@mobile.pishkhanak.com	\N	$2y$12$4s/BEcli4.rjGJlgHH0GZ.nynOo6nlpfGjVpNORt5ghqYRsbWSRQi	\N	2025-09-08 17:04:11	2025-09-08 17:04:11	\N	\N	\N	\N	09377373625	2025-09-08 17:04:11
144	کاربر 6550	09378276550@mobile.pishkhanak.com	\N	$2y$12$6/XWz93xZ/.qPw2PhoFVze4wdNAtdw/cdvEy3bMvp7oMV14zlL9oS	\N	2025-09-08 17:04:12	2025-09-08 17:04:12	\N	\N	\N	\N	09378276550	2025-09-08 17:04:11
145	کاربر 2449	09382142449@mobile.pishkhanak.com	\N	$2y$12$i46LSVttCN/w.A8iCxOn6u3lQdrhIB2NCANLF3vkK4XLagqczegzO	\N	2025-09-08 17:04:12	2025-09-08 17:04:12	\N	\N	\N	\N	09382142449	2025-09-08 17:04:12
146	کاربر 4561	09384544561@mobile.pishkhanak.com	\N	$2y$12$D9w3oz10zZz1ujxH0A7hzuBIOXM1OJ/t6EXxwwLwePHJfI6QCMoju	\N	2025-09-08 17:04:12	2025-09-08 17:04:12	\N	\N	\N	\N	09384544561	2025-09-08 17:04:12
147	کاربر 1403	09384701403@mobile.pishkhanak.com	\N	$2y$12$Gu2ZXXD/yY6pHBVlPr0sLuX2ui1LTxv.t6t17L1A6csHX8Tr7L29S	\N	2025-09-08 17:04:13	2025-09-08 17:04:13	\N	\N	\N	\N	09384701403	2025-09-08 17:04:12
148	کاربر 8062	09388868062@mobile.pishkhanak.com	\N	$2y$12$KEaNN6sApI44o/5KqUIsQORjMurwsWERVQddFmFjpW1GxP.HJCG0W	\N	2025-09-08 17:04:13	2025-09-08 17:04:13	\N	\N	\N	\N	09388868062	2025-09-08 17:04:13
149	کاربر 7572	09389957572@mobile.pishkhanak.com	\N	$2y$12$suGeLrDEsohQud0YA9ogBe6Sa4z5wt8ytdqp4HfCo1FNWZ3yGUfKS	\N	2025-09-08 17:04:13	2025-09-08 17:04:13	\N	\N	\N	\N	09389957572	2025-09-08 17:04:13
150	کاربر 1717	09390651717@mobile.pishkhanak.com	\N	$2y$12$rkR5tBJ1Iu1eXXKfHyscoeN.uORWIU8KGkJo9gZk.IuNWIQbU2R5a	\N	2025-09-08 17:04:13	2025-09-08 17:04:13	\N	\N	\N	\N	09390651717	2025-09-08 17:04:13
151	کاربر 3219	09395703219@mobile.pishkhanak.com	\N	$2y$12$FHW8Aw2c3uvByCvkzjwQSOKSpfpEpllKC0yUiP32PBs3kntwJx8wm	\N	2025-09-08 17:04:14	2025-09-08 17:04:14	\N	\N	\N	\N	09395703219	2025-09-08 17:04:13
152	کاربر 3084	09396203084@mobile.pishkhanak.com	\N	$2y$12$Zt5XrOzDIDGK4OmZDpumqeFr9N7dBdhLaMS.KKMwY6XH5AtlVAjRe	\N	2025-09-08 17:04:14	2025-09-08 17:04:14	\N	\N	\N	\N	09396203084	2025-09-08 17:04:14
153	کاربر 0476	09397760476@mobile.pishkhanak.com	\N	$2y$12$1OBWAVtalyyx4sbkyFFvGef2KFwMfS.n3H9340OecL1jLfLqOMEfy	\N	2025-09-08 17:04:14	2025-09-08 17:04:14	\N	\N	\N	\N	09397760476	2025-09-08 17:04:14
154	کاربر 0293	09867000293@mobile.pishkhanak.com	\N	$2y$12$HaswxQB/B6GghfcBENV.m.uZd8Kn8Zdu48esVLvSu4am4Ni0qP16e	\N	2025-09-08 17:04:14	2025-09-08 17:04:14	\N	\N	\N	\N	09867000293	2025-09-08 17:04:14
155	کاربر 6575	09903596575@mobile.pishkhanak.com	\N	$2y$12$Cw9tCCpyFVp3p1x3cn372uxRMOQvX/sEA4urh9zapD8GI0PTfUK92	\N	2025-09-08 17:04:15	2025-09-08 17:04:15	\N	\N	\N	\N	09903596575	2025-09-08 17:04:14
156	کاربر 0130	09903640130@mobile.pishkhanak.com	\N	$2y$12$hzgb5Pkr8.b1WH9e7sMOwuy25s3INQc80hXGxEuM54qv3U56iboYm	\N	2025-09-08 17:04:15	2025-09-08 17:04:15	\N	\N	\N	\N	09903640130	2025-09-08 17:04:15
157	کاربر 9221	09907849221@mobile.pishkhanak.com	\N	$2y$12$zUowRwRqS/ax1rCymUU2Jet3DXuvkMc1kucrxQncvBEcSpxtlV5sS	\N	2025-09-08 17:04:15	2025-09-08 17:04:15	\N	\N	\N	\N	09907849221	2025-09-08 17:04:15
158	کاربر 4750	09912504750@mobile.pishkhanak.com	\N	$2y$12$aVvQCqED5CTFyglDaL7IQuYH/ZTlcQKAh88SmDfC3TEAb7Rn96lJm	\N	2025-09-08 17:04:16	2025-09-08 17:04:16	\N	\N	\N	\N	09912504750	2025-09-08 17:04:15
159	کاربر 4843	09914554843@mobile.pishkhanak.com	\N	$2y$12$g.Bg1RwyG91DuvqLGgQfae2/6IlnkHZQgWZ7rzMCtXIYdSPJ5K7GO	\N	2025-09-08 17:04:16	2025-09-08 17:04:16	\N	\N	\N	\N	09914554843	2025-09-08 17:04:16
160	کاربر 0780	09915830780@mobile.pishkhanak.com	\N	$2y$12$Xl7HI874rFrypjmqUiSbSu8ksnItkCI5N4QmPkmW3cESm4.18lN6.	\N	2025-09-08 17:04:16	2025-09-08 17:04:16	\N	\N	\N	\N	09915830780	2025-09-08 17:04:16
161	کاربر 7036	09916897036@mobile.pishkhanak.com	\N	$2y$12$48eoFoM1ZnYDqC3F.jZdsu9lEHPNIl2VFlRm2wNrn4HIpUltmJLM2	\N	2025-09-08 17:04:16	2025-09-08 17:04:16	\N	\N	\N	\N	09916897036	2025-09-08 17:04:16
162	کاربر 4773	09918114773@mobile.pishkhanak.com	\N	$2y$12$we.SiuSK0B4SAFkrQSj0aOnCrQIg1F/Zx/2JQeQ.usBI6koTTCACG	\N	2025-09-08 17:04:17	2025-09-08 17:04:17	\N	\N	\N	\N	09918114773	2025-09-08 17:04:16
163	کاربر 3809	09918673809@mobile.pishkhanak.com	\N	$2y$12$7T4BhIGPP3/vfY8AynAUYOyPqb9iWHXof.yKlk/ihoNeIBX9rRVXu	\N	2025-09-08 17:04:17	2025-09-08 17:04:17	\N	\N	\N	\N	09918673809	2025-09-08 17:04:17
164	کاربر 2371	09922702371@mobile.pishkhanak.com	\N	$2y$12$W9Rkt7j4RiRQ9X1ahUhWqO9cKjeql0hSUZUjvjYa9Z19is.ktdpd6	\N	2025-09-08 17:04:17	2025-09-08 17:04:17	\N	\N	\N	\N	09922702371	2025-09-08 17:04:17
165	کاربر 3619	09922973619@mobile.pishkhanak.com	\N	$2y$12$UlfVVwJc0RlUZH/PYEodx.h.tnRJAODEJfDBodeyKODZrJ/8/iEE6	\N	2025-09-08 17:04:17	2025-09-08 17:04:17	\N	\N	\N	\N	09922973619	2025-09-08 17:04:17
166	کاربر 1488	09933121488@mobile.pishkhanak.com	\N	$2y$12$QPKBoPmzjeFlaEKoC/VaVu4qthJT6TuErBeo4XDP5UgeNZHo4H4b2	\N	2025-09-08 17:04:18	2025-09-08 17:04:18	\N	\N	\N	\N	09933121488	2025-09-08 17:04:17
167	کاربر 3358	09938023358@mobile.pishkhanak.com	\N	$2y$12$BFT39zVcZjuvWSp5zb7EjOFbJ1FLp.HpgoeEverXMwubZGazIXoja	\N	2025-09-08 17:04:18	2025-09-08 17:04:18	\N	\N	\N	\N	09938023358	2025-09-08 17:04:18
168	کاربر 6198	09938226198@mobile.pishkhanak.com	\N	$2y$12$FiBdHukS.3a5kbf8FpxCfOFauxaFTGxs5bkTpDfdB46XzPZMhADPe	\N	2025-09-08 17:04:18	2025-09-08 17:04:18	\N	\N	\N	\N	09938226198	2025-09-08 17:04:18
169	کاربر 6907	09938886907@mobile.pishkhanak.com	\N	$2y$12$e5m.mqRclGPPO0LbZYgs2eRMpvhz3IGWmibG3g3.l7jttfcN0BF0a	\N	2025-09-08 17:04:18	2025-09-08 17:04:18	\N	\N	\N	\N	09938886907	2025-09-08 17:04:18
170	کاربر 9526	09965729526@mobile.pishkhanak.com	\N	$2y$12$z.qCzzayuavTRK6jbNbTAOtdrHxPlJtbWcO1FSbFbGIFCditLDcBq	\N	2025-09-08 17:04:19	2025-09-08 17:04:19	\N	\N	\N	\N	09965729526	2025-09-08 17:04:18
171	کاربر 4125	09055374125@mobile.pishkhanak.com	\N	\N	eE1xUtR3FCyxBXiBmpc6BXrEcVA6j4P2mk0bMJhNt5bLEenCY7dgm6mD2DZf	2025-09-08 17:13:29	2025-09-08 17:13:29	\N	\N	\N	\N	09055374125	2025-09-08 17:13:29
172	کاربر 2001	09134202001@mobile.pishkhanak.com	\N	\N	mR9du3jyalmWbmalF3LTdJ4rLuc0FT3AYSlSnvCL4xQS2UK0VJYDUEYSsKzR	2025-09-08 17:19:09	2025-09-08 17:19:09	\N	\N	\N	\N	09134202001	2025-09-08 17:19:09
173	کاربر 1645	09126831645@mobile.pishkhanak.com	\N	\N	LoLtbJObmeJXDY2JFc6qP0F7im9Mrbi30HXUSc0bJrqP3pP0qv2jixFRGZlo	2025-09-08 17:40:12	2025-09-08 17:40:12	\N	\N	\N	\N	09126831645	2025-09-08 17:40:12
174	کاربر 4820	09165544820@mobile.pishkhanak.com	\N	\N	PX4Fo7dt18s7xopnb5pC9eFFk1ZA7cnpiaQk0jXLZUsMnZRmD8bHlamLz0AA	2025-09-08 18:00:18	2025-09-08 18:00:18	\N	\N	\N	\N	09165544820	2025-09-08 18:00:18
175	کاربر 9669	09305939669@mobile.pishkhanak.com	\N	\N	yDIbKwZSefTQGbjlyAynkUR0E5jOhp7uDov9WlkcqDRL8YFgtiOKwb3QqXrZ	2025-09-08 18:05:52	2025-09-08 18:05:52	\N	\N	\N	\N	09305939669	2025-09-08 18:05:52
176	کاربر 0933	09184640933@mobile.pishkhanak.com	\N	\N	53B9AVg6QrLRNoqrfJ3wafqTGO9D4M9P2qyvCn1UePpR2j8hBUXn8fbw6jGk	2025-09-08 18:06:41	2025-09-08 18:06:41	\N	\N	\N	\N	09184640933	2025-09-08 18:06:41
50	کاربر 2349	09124102349@mobile.pishkhanak.com	\N	$2y$12$aL.hd.IgORgGKDJ6GDVFqOsxQuZMW/MGcy9Yx85bKJ9XMOQaza2dW	yuS0hZmbGvfRvHBik11JbyZJefwFCHWkUspSRV4ML6NpagvFRSL3LH1ZUxMt	2025-09-08 17:03:46	2025-09-08 17:03:46	\N	\N	\N	\N	09124102349	2025-09-08 17:03:46
177	کاربر 5838	09144545838@mobile.pishkhanak.com	\N	\N	sOzKWKSvE2D078GY2DJfEVSUpIaTQ5AGVDTHQ3MwmMhVV27fRUvb31kGK9XV	2025-09-08 18:14:29	2025-09-08 18:14:29	\N	\N	\N	\N	09144545838	2025-09-08 18:14:29
113	کاربر 1549	09197131549@mobile.pishkhanak.com	\N	$2y$12$JU3nt2yzOZqq5xAMHwLuc.6WJkZH481zM1.VTic8.Lu/662PARy5m	FChRJkpLLqSYfA2TALxruUBruqG0upFbBMcXGpQlnlu1uCRyRQO90RRVKF0h	2025-09-08 17:04:03	2025-09-08 17:04:03	\N	\N	\N	\N	09197131549	2025-09-08 17:04:03
178	کاربر 5339	09185585339@mobile.pishkhanak.com	\N	\N	OvXJ3AzN92hBNSIiU70h6v1sWbt9T6rh4kKUWHoEWOyoYYSKlPgCIShVCTFS	2025-09-08 18:35:32	2025-09-08 18:35:32	\N	\N	\N	\N	09185585339	2025-09-08 18:35:32
179	کاربر 6298	09366506298@mobile.pishkhanak.com	\N	\N	nNzfJaL6heh6IM6r8fBCuFP1eA15UdBGMXs1R3IsVRtbflOjw1NTf6BRAlWA	2025-09-08 18:49:27	2025-09-08 18:49:27	\N	\N	\N	\N	09366506298	2025-09-08 18:49:27
180	کاربر 8684	09364968684@mobile.pishkhanak.com	\N	\N	CXnGW6bGnOQi63y2McGKiuy8saNaKIS3BC9KFJVXKhpCODKysvv1GbAqKs41	2025-09-08 18:52:03	2025-09-08 18:52:03	\N	\N	\N	\N	09364968684	2025-09-08 18:52:03
181	کاربر 1121	09102621121@mobile.pishkhanak.com	\N	\N	KT9rUq5Ib98eiT3P4lSJauRzNlzEZslRILtiDbg2DUQWAqX3SbfRfPdsDqWN	2025-09-08 18:53:44	2025-09-08 18:53:44	\N	\N	\N	\N	09102621121	2025-09-08 18:53:44
90	کاربر 0399	09168600399@mobile.pishkhanak.com	\N	$2y$12$xcIktcSUU7inADnhPpgnseSbicO1UjlXW/xskn./PlExCXvpLa/Wi	fSFlTn4mypr3EkA87pOia8nSg5isSPaiPRHikKERYaoltLtbjb8212BP0ZV8	2025-09-08 17:03:57	2025-09-08 17:03:57	\N	\N	\N	\N	09168600399	2025-09-08 17:03:56
182	کاربر 3415	09197103415@mobile.pishkhanak.com	\N	\N	vjvGh3wWjfa6VNIRDsBurNLyzjgHxGtB0fNCxukDyboQYTv1wcg0y15U6aSP	2025-09-08 19:12:40	2025-09-08 19:12:40	\N	\N	\N	\N	09197103415	2025-09-08 19:12:40
183	کاربر 8205	09149158205@mobile.pishkhanak.com	\N	\N	tYCo6goEedsxX0tvAgJ2lBjT7ymY4z6mHSytgvqoYriKxypUXP8Nlq8h2sPh	2025-09-08 19:17:11	2025-09-08 19:17:11	\N	\N	\N	\N	09149158205	2025-09-08 19:17:11
184	کاربر 8330	09131548330@mobile.pishkhanak.com	\N	\N	aBfAz4R0jKclhtJ6Gys5XUvkgK28p4T8SBx1c9VdBCQfYRYuomErl6cXwc0R	2025-09-08 19:20:46	2025-09-08 19:20:46	\N	\N	\N	\N	09131548330	2025-09-08 19:20:46
185	کاربر 2157	09369162157@mobile.pishkhanak.com	\N	\N	AYHaUXI3hPWpv8N7T1WvgPjV5Tddzk3Q1vqD2RwiNasDNcoGsNVUIZte7Yff	2025-09-08 19:39:00	2025-09-08 19:39:00	\N	\N	\N	\N	09369162157	2025-09-08 19:39:00
186	کاربر 3820	09026603820@mobile.pishkhanak.com	\N	\N	B31rEBQMFgz1On8LKtO54hdoRj6fySBg3vhfuVvOVGMWpgXk6ezu2Xnn7Gz0	2025-09-08 19:59:00	2025-09-08 19:59:00	\N	\N	\N	\N	09026603820	2025-09-08 19:59:00
5	کاربر 7809	09153887809@mobile.pishkhanak.com	\N	$2y$12$PVqoBzy.xBhWpp1azfKCdOO1dXtNM8jykShx00Us4He87Y18p.iGK	MCWir18nKsBj9E4iKViK7Af8ZSonN4H2rxD82cfvNEvg547bS6NdgCaiUKTn	2025-09-08 16:52:27	2025-09-08 20:05:11	\N	\N	\N	\N	09153887809	2025-07-02 10:22:44
187	کاربر 2032	09197302032@mobile.pishkhanak.com	\N	\N	HV3Mz5FEAd6bAlyczkUTn3SikMLzt92aIse68qLMkyJohywHsx2H3tx7hYYe	2025-09-08 20:07:10	2025-09-08 20:07:10	\N	\N	\N	\N	09197302032	2025-09-08 20:07:10
188	کاربر 4360	09195664360@mobile.pishkhanak.com	\N	\N	G1kQYdBqVHJfd3pM3S9xQLteNb1j1nvRXJQXHsZD8imrLCAAenZaqsjcPw8j	2025-09-08 20:15:25	2025-09-08 20:15:25	\N	\N	\N	\N	09195664360	2025-09-08 20:15:25
189	کاربر 2188	09153442188@mobile.pishkhanak.com	\N	\N	9NVrugSN9SaSUi7hjwrJF4nT2uoiuJvn5TWIy7JhtJsKOr2g5G5dApDKsuTt	2025-09-08 20:24:54	2025-09-08 20:24:54	\N	\N	\N	\N	09153442188	2025-09-08 20:24:54
\.


--
-- Data for Name: wallet_audit_logs; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.wallet_audit_logs (id, wallet_id, admin_id, action, amount, old_balance, new_balance, reason, reference_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: wallets; Type: TABLE DATA; Schema: public; Owner: ali_master
--

COPY public.wallets (id, holder_type, holder_id, name, slug, uuid, description, meta, balance, decimal_places, created_at, updated_at, deleted_at) FROM stdin;
1	App\\Models\\User	2	اصلی	main-2	7a392a7f-6605-4ea7-bac9-6ebd0dbeb60d	\N	\N	10000000	2	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
2	App\\Models\\User	4	اصلی	main-4	96ebda89-446a-49a9-8252-31612b8b5077	\N	\N	10000000	2	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
3	App\\Models\\User	5	اصلی	main-5	1c9a7836-1e80-4f7a-94ef-618bc228e71b	\N	\N	10000000	2	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
4	App\\Models\\User	6	اصلی	main-6	a5513f1e-0a2c-45e8-b70c-3b2bd67feadd	\N	\N	10000000	2	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
5	App\\Models\\User	7	اصلی	main-7	7b7ceda0-c255-4e87-9182-8d26bf9439c6	\N	\N	10000000	2	2025-09-08 16:53:16	2025-09-08 16:53:16	\N
6	App\\Models\\User	9	اصلی	main-9	5246ccc3-1d5e-41d3-9d2a-1f8013af6bbc	\N	\N	10000000	2	2025-09-08 17:03:35	2025-09-08 17:03:35	\N
7	App\\Models\\User	10	اصلی	main-10	bd5dbc8d-95e9-491c-b6f5-ea6668fca15c	\N	\N	10000000	2	2025-09-08 17:03:35	2025-09-08 17:03:35	\N
8	App\\Models\\User	11	اصلی	main-11	15a6625e-3ea0-49e1-812c-720fedb14baf	\N	\N	10000000	2	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
9	App\\Models\\User	12	اصلی	main-12	25138192-2e90-4a90-86eb-28df9014afef	\N	\N	10000000	2	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
10	App\\Models\\User	13	اصلی	main-13	89ebbfe1-51ee-4955-9a0e-1ab8252f54af	\N	\N	10000000	2	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
11	App\\Models\\User	14	اصلی	main-14	7ee96b44-4bb6-4a67-98ec-a7c222260a1f	\N	\N	10000000	2	2025-09-08 17:03:36	2025-09-08 17:03:36	\N
12	App\\Models\\User	15	اصلی	main-15	e6b8edc2-9634-448a-8ca7-38747f10ab0f	\N	\N	10000000	2	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
13	App\\Models\\User	16	اصلی	main-16	fd0980d6-d7b8-4a7e-bf9a-819a6670c313	\N	\N	10000000	2	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
14	App\\Models\\User	17	اصلی	main-17	e09a3b62-4a9d-44fb-9479-c10e72fed0e5	\N	\N	10000000	2	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
15	App\\Models\\User	18	اصلی	main-18	14daa8cc-2268-40d0-bc3f-25e04f36ffad	\N	\N	10000000	2	2025-09-08 17:03:37	2025-09-08 17:03:37	\N
16	App\\Models\\User	19	اصلی	main-19	01089071-0845-4f8b-9845-f6f9dea394dc	\N	\N	10000000	2	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
17	App\\Models\\User	20	اصلی	main-20	a321085a-9610-4759-aff1-23a42f3e3bc2	\N	\N	10000000	2	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
18	App\\Models\\User	21	اصلی	main-21	8b828a88-e102-4b2c-aacc-64abeae7176e	\N	\N	10000000	2	2025-09-08 17:03:38	2025-09-08 17:03:38	\N
19	App\\Models\\User	22	اصلی	main-22	18b8b3ea-6355-4647-8b0e-2589d8e6cf62	\N	\N	10000000	2	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
20	App\\Models\\User	23	اصلی	main-23	2f2e7dc0-b9ad-4abe-b676-f133ce6e73c0	\N	\N	10000000	2	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
21	App\\Models\\User	24	اصلی	main-24	46b0efe3-7a3b-4cc9-b985-ba8100903168	\N	\N	10000000	2	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
22	App\\Models\\User	25	اصلی	main-25	439fac70-d994-4071-ac22-87b5e5c4c879	\N	\N	10000000	2	2025-09-08 17:03:39	2025-09-08 17:03:39	\N
23	App\\Models\\User	26	اصلی	main-26	eb977404-b230-485c-9af9-7be65a8c9bfa	\N	\N	10000000	2	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
24	App\\Models\\User	27	اصلی	main-27	bc34da1f-ac2d-4cd4-8ce3-284d590cd840	\N	\N	10000000	2	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
25	App\\Models\\User	28	اصلی	main-28	dda90570-9cca-4eb9-8abb-5bed85591bb7	\N	\N	10000000	2	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
26	App\\Models\\User	29	اصلی	main-29	a93fd349-2bad-458b-90ff-ca3cc0a2c07f	\N	\N	10000000	2	2025-09-08 17:03:40	2025-09-08 17:03:40	\N
27	App\\Models\\User	30	اصلی	main-30	f8c330b7-0d27-4bb9-9b10-92c61b16bd74	\N	\N	10000000	2	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
28	App\\Models\\User	31	اصلی	main-31	37a1c828-e937-4157-a0b1-6bdf2562af7b	\N	\N	10000000	2	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
29	App\\Models\\User	32	اصلی	main-32	b52f89a5-5a02-448e-b78e-719e09848230	\N	\N	10000000	2	2025-09-08 17:03:41	2025-09-08 17:03:41	\N
30	App\\Models\\User	33	اصلی	main-33	cc927f00-7895-4e24-b84d-ddb189cc9c4d	\N	\N	10000000	2	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
31	App\\Models\\User	34	اصلی	main-34	af316563-7160-42a7-96ea-3f0aab4678b9	\N	\N	10000000	2	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
32	App\\Models\\User	35	اصلی	main-35	bcab59f2-0ed9-44ed-ba92-b1600dab1b32	\N	\N	10000000	2	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
33	App\\Models\\User	36	اصلی	main-36	b359321f-1418-4142-acc8-05a2a6191f7e	\N	\N	10000000	2	2025-09-08 17:03:42	2025-09-08 17:03:42	\N
34	App\\Models\\User	37	اصلی	main-37	4452a744-d308-43d2-8648-7e433805325f	\N	\N	10000000	2	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
35	App\\Models\\User	38	اصلی	main-38	ee4cb374-a810-4eb1-b7dc-9f1ca983bd27	\N	\N	10000000	2	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
36	App\\Models\\User	39	اصلی	main-39	8df452ce-3783-4b95-b09e-fdf2b2d542c6	\N	\N	10000000	2	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
37	App\\Models\\User	40	اصلی	main-40	e1ab9a47-97c6-4ef0-88ad-0123e1872fb2	\N	\N	10000000	2	2025-09-08 17:03:43	2025-09-08 17:03:43	\N
38	App\\Models\\User	41	اصلی	main-41	3750aa58-0ab6-4504-9ec5-6948831738c1	\N	\N	10000000	2	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
39	App\\Models\\User	42	اصلی	main-42	1416d78a-25a7-4238-96d8-2d1d79c9c259	\N	\N	10000000	2	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
40	App\\Models\\User	43	اصلی	main-43	f949c565-179b-44ca-9d8c-8063d1a0f9ed	\N	\N	10000000	2	2025-09-08 17:03:44	2025-09-08 17:03:44	\N
41	App\\Models\\User	44	اصلی	main-44	d8772e2c-ca59-40b6-912a-992855513994	\N	\N	10000000	2	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
42	App\\Models\\User	45	اصلی	main-45	595ad200-62c8-4ba7-8a82-a1cfce21605c	\N	\N	10000000	2	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
43	App\\Models\\User	46	اصلی	main-46	6e5375a0-0c93-44d1-9577-2dbb1e0e64e8	\N	\N	10000000	2	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
44	App\\Models\\User	47	اصلی	main-47	ca1bc415-a0e7-40c9-b981-1bc0b113ecef	\N	\N	10000000	2	2025-09-08 17:03:45	2025-09-08 17:03:45	\N
45	App\\Models\\User	48	اصلی	main-48	46089fc9-d3b7-4ad3-badc-cc735ba3708d	\N	\N	10000000	2	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
46	App\\Models\\User	49	اصلی	main-49	ab3936cd-a9aa-4c60-8a2b-9416578e68c3	\N	\N	10000000	2	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
47	App\\Models\\User	50	اصلی	main-50	1f1b9f76-2bc1-4008-815f-178fdb7bf649	\N	\N	10000000	2	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
48	App\\Models\\User	51	اصلی	main-51	31a11662-8548-45ee-8c65-54062f82fba3	\N	\N	10000000	2	2025-09-08 17:03:46	2025-09-08 17:03:46	\N
49	App\\Models\\User	52	اصلی	main-52	c35bf8db-183c-4ba7-b8bb-7feb89ae37b1	\N	\N	10000000	2	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
50	App\\Models\\User	53	اصلی	main-53	e9ef2531-bf12-4b3e-8718-6c291f9f109d	\N	\N	10000000	2	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
51	App\\Models\\User	54	اصلی	main-54	f39f5ee1-ecb1-4042-9133-b98166024b7f	\N	\N	10000000	2	2025-09-08 17:03:47	2025-09-08 17:03:47	\N
52	App\\Models\\User	55	اصلی	main-55	e1bf8d5d-eaf6-4b80-8d09-9557f18d500b	\N	\N	10000000	2	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
53	App\\Models\\User	56	اصلی	main-56	e01dfb6d-4c76-4089-b0fb-9c324322e4ba	\N	\N	10000000	2	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
54	App\\Models\\User	57	اصلی	main-57	f7021558-22df-471c-8265-b2fb034905fa	\N	\N	10000000	2	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
55	App\\Models\\User	58	اصلی	main-58	026d3279-9034-423a-8819-9f7acd40dca9	\N	\N	10000000	2	2025-09-08 17:03:48	2025-09-08 17:03:48	\N
56	App\\Models\\User	59	اصلی	main-59	933dffa8-6e6a-47d9-b6c3-b45541ae5ad7	\N	\N	10000000	2	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
57	App\\Models\\User	60	اصلی	main-60	3165da83-9a80-44de-be0b-8269d6713c31	\N	\N	10000000	2	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
58	App\\Models\\User	61	اصلی	main-61	68c7f217-c568-4dd7-9ab0-ba8be4ea7ab5	\N	\N	10000000	2	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
59	App\\Models\\User	62	اصلی	main-62	46bac677-df5c-497e-a4e5-9a2df4a3027f	\N	\N	10000000	2	2025-09-08 17:03:49	2025-09-08 17:03:49	\N
60	App\\Models\\User	63	اصلی	main-63	2e0739df-99ca-4cde-bb72-4857d0f7cdc5	\N	\N	10000000	2	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
61	App\\Models\\User	64	اصلی	main-64	8d01d962-f30d-43b4-a06c-f00a17b28f06	\N	\N	10000000	2	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
62	App\\Models\\User	65	اصلی	main-65	6a62f81a-34db-4be2-9d3b-8363ff817519	\N	\N	10000000	2	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
63	App\\Models\\User	66	اصلی	main-66	f5cb388e-f751-4b00-837d-b24ad84cdb52	\N	\N	10000000	2	2025-09-08 17:03:50	2025-09-08 17:03:50	\N
64	App\\Models\\User	67	اصلی	main-67	9c0a74a3-7c41-45c9-81f8-52cf3d39bba2	\N	\N	10000000	2	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
65	App\\Models\\User	68	اصلی	main-68	920e5cf4-da55-4f58-9b92-d97ca991130d	\N	\N	10000000	2	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
66	App\\Models\\User	69	اصلی	main-69	9cf3388e-139e-4db4-9a71-f4e55004067e	\N	\N	10000000	2	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
67	App\\Models\\User	70	اصلی	main-70	61701c40-9e22-4b91-96a7-c24427f0b587	\N	\N	10000000	2	2025-09-08 17:03:51	2025-09-08 17:03:51	\N
68	App\\Models\\User	71	اصلی	main-71	fb479fd3-aec2-4bff-b0c7-4f0b8c86297b	\N	\N	10000000	2	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
69	App\\Models\\User	72	اصلی	main-72	b1c8387b-ae5a-46d2-b9d0-92ba0cb0f449	\N	\N	10000000	2	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
70	App\\Models\\User	73	اصلی	main-73	e83dde51-0f1c-4840-8bc7-7ce17fad3ad5	\N	\N	10000000	2	2025-09-08 17:03:52	2025-09-08 17:03:52	\N
71	App\\Models\\User	74	اصلی	main-74	ec1d7ef7-738f-4d71-ba07-d3afabfa6ab3	\N	\N	10000000	2	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
72	App\\Models\\User	75	اصلی	main-75	12ca3416-9171-4126-b421-d6fcf44cc6a9	\N	\N	10000000	2	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
73	App\\Models\\User	76	اصلی	main-76	48040f0c-87c0-4410-8c68-432c03611a6c	\N	\N	10000000	2	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
74	App\\Models\\User	77	اصلی	main-77	c1e400ed-d026-41c8-a750-76a0201fa20d	\N	\N	10000000	2	2025-09-08 17:03:53	2025-09-08 17:03:53	\N
75	App\\Models\\User	78	اصلی	main-78	2817fad3-3040-4315-a79c-54debbccbdeb	\N	\N	10000000	2	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
76	App\\Models\\User	79	اصلی	main-79	a96dac14-5791-4455-841d-9048f9f91070	\N	\N	10000000	2	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
77	App\\Models\\User	80	اصلی	main-80	f4b06cbb-7819-4b42-9350-e3a70f28b6f5	\N	\N	10000000	2	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
78	App\\Models\\User	81	اصلی	main-81	ddb2cc06-3c87-4db4-bd87-fd9f6d848e77	\N	\N	10000000	2	2025-09-08 17:03:54	2025-09-08 17:03:54	\N
79	App\\Models\\User	82	اصلی	main-82	c039e215-a581-404b-9cc3-599b7c42f84a	\N	\N	10000000	2	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
80	App\\Models\\User	83	اصلی	main-83	ba0c93c5-ecaf-4136-9144-f2c002e621e8	\N	\N	10000000	2	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
81	App\\Models\\User	84	اصلی	main-84	d35c31c4-02c9-4d53-9127-2717cd72d317	\N	\N	10000000	2	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
82	App\\Models\\User	85	اصلی	main-85	b5a1b98f-0e8d-42dc-9ee7-6b710523006a	\N	\N	10000000	2	2025-09-08 17:03:55	2025-09-08 17:03:55	\N
83	App\\Models\\User	86	اصلی	main-86	82e24314-3a06-43b4-8804-78463b699eeb	\N	\N	10000000	2	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
84	App\\Models\\User	87	اصلی	main-87	0c374d3f-7bdb-405d-be8d-25f87d707fcf	\N	\N	10000000	2	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
85	App\\Models\\User	88	اصلی	main-88	f63ce2c9-6179-42b4-8781-bace80cb8b5c	\N	\N	10000000	2	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
86	App\\Models\\User	89	اصلی	main-89	ec819981-6dc2-4aee-8d79-50924573822b	\N	\N	10000000	2	2025-09-08 17:03:56	2025-09-08 17:03:56	\N
87	App\\Models\\User	90	اصلی	main-90	2f5687f6-2356-4064-be94-f9a1b94b055f	\N	\N	10000000	2	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
88	App\\Models\\User	91	اصلی	main-91	b5fbd396-3ea4-4b29-91d4-2e8f82e21801	\N	\N	10000000	2	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
89	App\\Models\\User	92	اصلی	main-92	b0d21ae8-29f4-45c9-aab2-347cde4f5a28	\N	\N	10000000	2	2025-09-08 17:03:57	2025-09-08 17:03:57	\N
90	App\\Models\\User	93	اصلی	main-93	3a5c4d4b-ecd4-4724-93d2-69ff682c0e2c	\N	\N	10000000	2	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
91	App\\Models\\User	94	اصلی	main-94	ee22636c-1444-4c14-b59c-77e0a93991b2	\N	\N	10000000	2	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
92	App\\Models\\User	95	اصلی	main-95	9c73d124-d2e4-46cc-bd75-850374be3e8a	\N	\N	10000000	2	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
93	App\\Models\\User	96	اصلی	main-96	e7531474-f8ad-45c9-91f9-e39c15699ca8	\N	\N	10000000	2	2025-09-08 17:03:58	2025-09-08 17:03:58	\N
94	App\\Models\\User	97	اصلی	main-97	e71acc5d-07ee-4aca-9c35-cde7cab37311	\N	\N	10000000	2	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
95	App\\Models\\User	98	اصلی	main-98	28f13e4f-666a-4e47-8af5-821ad53b6d2c	\N	\N	10000000	2	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
96	App\\Models\\User	99	اصلی	main-99	e0af7c6f-8aab-4dd2-b57b-f56132a9c769	\N	\N	10000000	2	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
97	App\\Models\\User	100	اصلی	main-100	6f749933-bf92-4af1-b34e-feb105afa1fd	\N	\N	10000000	2	2025-09-08 17:03:59	2025-09-08 17:03:59	\N
98	App\\Models\\User	101	اصلی	main-101	2639cc0a-39bd-4699-a837-243e4e9de5b0	\N	\N	10000000	2	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
99	App\\Models\\User	102	اصلی	main-102	776e50a0-33fe-44bd-86e9-ba68171637ab	\N	\N	10000000	2	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
100	App\\Models\\User	103	اصلی	main-103	9510809d-fcef-4e02-b263-9adc96c87cb0	\N	\N	10000000	2	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
101	App\\Models\\User	104	اصلی	main-104	dae40008-a646-47ed-a63a-3c477591121f	\N	\N	10000000	2	2025-09-08 17:04:00	2025-09-08 17:04:00	\N
102	App\\Models\\User	105	اصلی	main-105	a79981a2-8013-4491-a498-d8ad20012a02	\N	\N	10000000	2	2025-09-08 17:04:01	2025-09-08 17:04:01	\N
103	App\\Models\\User	106	اصلی	main-106	e9481816-eb7e-4da7-bc27-7e54da5d3918	\N	\N	10000000	2	2025-09-08 17:04:01	2025-09-08 17:04:01	\N
104	App\\Models\\User	107	اصلی	main-107	7b5327e7-6c5d-45c0-92f8-196a1db5ef66	\N	\N	10000000	2	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
105	App\\Models\\User	108	اصلی	main-108	346bd092-5882-4e7f-ad65-6976b4a4a585	\N	\N	10000000	2	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
106	App\\Models\\User	109	اصلی	main-109	f484812a-a346-4478-b0a0-0a4a4aa598cf	\N	\N	10000000	2	2025-09-08 17:04:02	2025-09-08 17:04:02	\N
107	App\\Models\\User	110	اصلی	main-110	c3bbaf2e-7e3a-4a22-85e5-8805f4063aa4	\N	\N	10000000	2	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
108	App\\Models\\User	111	اصلی	main-111	d50d6fd7-203e-4084-bad1-f1da870953d5	\N	\N	10000000	2	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
109	App\\Models\\User	112	اصلی	main-112	fa37205e-0d23-4ce6-b7f4-620ecb826dda	\N	\N	10000000	2	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
110	App\\Models\\User	113	اصلی	main-113	c42800de-1f04-4bbd-b17a-1af4c993281d	\N	\N	10000000	2	2025-09-08 17:04:03	2025-09-08 17:04:03	\N
111	App\\Models\\User	114	اصلی	main-114	41cdad1e-14bd-4a23-951f-89de0b9171ad	\N	\N	10000000	2	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
112	App\\Models\\User	115	اصلی	main-115	5742146b-3f81-4459-a6b6-93fbe45fe5c3	\N	\N	10000000	2	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
113	App\\Models\\User	116	اصلی	main-116	2835af57-37b8-4161-a47b-f0e4ae611500	\N	\N	10000000	2	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
114	App\\Models\\User	117	اصلی	main-117	01d32e8a-1799-4fe1-b8ed-5d7457f2c855	\N	\N	10000000	2	2025-09-08 17:04:04	2025-09-08 17:04:04	\N
115	App\\Models\\User	118	اصلی	main-118	2b4ee4e8-d239-42df-b8a3-c4b7ea218fb9	\N	\N	10000000	2	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
116	App\\Models\\User	119	اصلی	main-119	d8847384-1285-4526-a65d-06e950de7d20	\N	\N	10000000	2	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
117	App\\Models\\User	120	اصلی	main-120	402d11d0-4844-46c4-b353-152bacaab253	\N	\N	10000000	2	2025-09-08 17:04:05	2025-09-08 17:04:05	\N
118	App\\Models\\User	121	اصلی	main-121	728b959d-8c28-4963-9b54-fbe52af4048b	\N	\N	10000000	2	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
119	App\\Models\\User	122	اصلی	main-122	284b23de-3d67-4a42-b544-b5bfae2b6d78	\N	\N	10000000	2	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
120	App\\Models\\User	123	اصلی	main-123	0a4bd3ec-6403-4961-af06-ddc746f447f7	\N	\N	10000000	2	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
121	App\\Models\\User	124	اصلی	main-124	b8f6a128-fbbc-47d7-b802-c5e1ac69dc8d	\N	\N	10000000	2	2025-09-08 17:04:06	2025-09-08 17:04:06	\N
122	App\\Models\\User	125	اصلی	main-125	ab04dfce-f763-4ea0-844b-c41e0f7b144e	\N	\N	10000000	2	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
123	App\\Models\\User	126	اصلی	main-126	f25834b1-891b-47fe-a997-fc75d1988a2c	\N	\N	10000000	2	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
124	App\\Models\\User	127	اصلی	main-127	57c42812-b169-41de-9c6e-a8b81ccd118a	\N	\N	10000000	2	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
125	App\\Models\\User	128	اصلی	main-128	5d18b4ec-3a32-414c-9b90-23b9bec9e2a6	\N	\N	10000000	2	2025-09-08 17:04:07	2025-09-08 17:04:07	\N
126	App\\Models\\User	129	اصلی	main-129	a0283738-ab59-4145-8010-e0c647ba4d8d	\N	\N	10000000	2	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
127	App\\Models\\User	130	اصلی	main-130	ddb9ef5d-c8e3-4f5a-a548-ac3635ad9cd3	\N	\N	10000000	2	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
128	App\\Models\\User	131	اصلی	main-131	ef8ca783-0ab1-402c-8115-a227ce623be2	\N	\N	10000000	2	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
129	App\\Models\\User	132	اصلی	main-132	71022c10-c6ee-41c2-abdd-3a37df6bfed0	\N	\N	10000000	2	2025-09-08 17:04:08	2025-09-08 17:04:08	\N
130	App\\Models\\User	133	اصلی	main-133	66502d9c-44c6-4ca4-8928-41813cc6c21c	\N	\N	10000000	2	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
131	App\\Models\\User	134	اصلی	main-134	85a26746-cb72-4d29-80bc-1593d29bdfc6	\N	\N	10000000	2	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
132	App\\Models\\User	135	اصلی	main-135	7f7a395f-9555-4d93-88aa-acc7eb8733bd	\N	\N	10000000	2	2025-09-08 17:04:09	2025-09-08 17:04:09	\N
133	App\\Models\\User	136	اصلی	main-136	720ba2a7-8ab7-4078-8da2-7b3f041d2129	\N	\N	10000000	2	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
134	App\\Models\\User	137	اصلی	main-137	d08de731-951b-4df5-872a-a38845b0159e	\N	\N	10000000	2	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
135	App\\Models\\User	138	اصلی	main-138	c881a0e4-8ade-4648-a0f2-4a0473d97081	\N	\N	10000000	2	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
136	App\\Models\\User	139	اصلی	main-139	6718f453-33aa-4d89-a42a-886fba30e17b	\N	\N	10000000	2	2025-09-08 17:04:10	2025-09-08 17:04:10	\N
137	App\\Models\\User	140	اصلی	main-140	9c7da098-d0bb-46e5-8781-c558349fc0c0	\N	\N	10000000	2	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
138	App\\Models\\User	141	اصلی	main-141	3428de9f-a3e1-496d-86e8-a846a994913c	\N	\N	10000000	2	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
139	App\\Models\\User	142	اصلی	main-142	a59b660d-b120-4a81-9072-d0d414657437	\N	\N	10000000	2	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
140	App\\Models\\User	143	اصلی	main-143	1382fa88-0c65-4238-9b23-a71cccdceaaa	\N	\N	10000000	2	2025-09-08 17:04:11	2025-09-08 17:04:11	\N
141	App\\Models\\User	144	اصلی	main-144	e92e4d9a-3fe4-4eb1-afe5-9a577d4dea3d	\N	\N	10000000	2	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
142	App\\Models\\User	145	اصلی	main-145	9755a486-a955-4365-8680-396829be3b11	\N	\N	10000000	2	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
143	App\\Models\\User	146	اصلی	main-146	81c5f05e-e820-41c6-9d2f-6b3d3af08456	\N	\N	10000000	2	2025-09-08 17:04:12	2025-09-08 17:04:12	\N
144	App\\Models\\User	147	اصلی	main-147	6394e608-a079-438d-9ad7-8811aae55553	\N	\N	10000000	2	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
145	App\\Models\\User	148	اصلی	main-148	51d41c03-6cc7-409c-a9bc-3eddf2a0535e	\N	\N	10000000	2	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
146	App\\Models\\User	149	اصلی	main-149	2d128b13-d907-48b7-8da4-a28e8bfe3b67	\N	\N	10000000	2	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
147	App\\Models\\User	150	اصلی	main-150	dfe892de-f83d-43b3-8f3f-52c5314da8b5	\N	\N	10000000	2	2025-09-08 17:04:13	2025-09-08 17:04:13	\N
148	App\\Models\\User	151	اصلی	main-151	f3d9234a-b159-4eb0-aa61-87e64d0aa8d3	\N	\N	10000000	2	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
149	App\\Models\\User	152	اصلی	main-152	74e70954-0b46-4486-9ce7-3790cb544601	\N	\N	10000000	2	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
150	App\\Models\\User	153	اصلی	main-153	13b52896-9e9c-4c67-86fe-d58d200c1706	\N	\N	10000000	2	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
151	App\\Models\\User	154	اصلی	main-154	5e097ea2-30c3-4659-8a14-d0d488a9ff13	\N	\N	10000000	2	2025-09-08 17:04:14	2025-09-08 17:04:14	\N
152	App\\Models\\User	155	اصلی	main-155	9dd372c8-adfc-40d7-b6ea-750ab29e873e	\N	\N	10000000	2	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
153	App\\Models\\User	156	اصلی	main-156	64e83dff-23be-4827-a612-bb13455dec7f	\N	\N	10000000	2	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
154	App\\Models\\User	157	اصلی	main-157	fba7c0b8-f728-4e0f-98b1-9d51a2e81550	\N	\N	10000000	2	2025-09-08 17:04:15	2025-09-08 17:04:15	\N
155	App\\Models\\User	158	اصلی	main-158	45d93a25-1339-4e39-84f9-dbb2d5cb66a4	\N	\N	10000000	2	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
156	App\\Models\\User	159	اصلی	main-159	592697d3-1912-42ea-8463-e79e0ec99060	\N	\N	10000000	2	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
157	App\\Models\\User	160	اصلی	main-160	12fbd348-7ea3-497f-859c-511d94f8acf6	\N	\N	10000000	2	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
158	App\\Models\\User	161	اصلی	main-161	33dce2ab-32c6-41ae-9525-fb2f31fcd832	\N	\N	10000000	2	2025-09-08 17:04:16	2025-09-08 17:04:16	\N
159	App\\Models\\User	162	اصلی	main-162	e7bd1a67-858d-44f1-b943-e90c44219959	\N	\N	10000000	2	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
160	App\\Models\\User	163	اصلی	main-163	0da693b8-7c9e-4974-80fd-1e8fc9dd8b87	\N	\N	10000000	2	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
161	App\\Models\\User	164	اصلی	main-164	8ff3d72c-81ac-4077-a33c-f34f8d7ff37f	\N	\N	10000000	2	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
162	App\\Models\\User	165	اصلی	main-165	ec9669f5-992e-4acf-aaa4-7d9cfc0936c6	\N	\N	10000000	2	2025-09-08 17:04:17	2025-09-08 17:04:17	\N
163	App\\Models\\User	166	اصلی	main-166	bfd2d080-ced3-400c-b1db-4a84cc07434d	\N	\N	10000000	2	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
164	App\\Models\\User	167	اصلی	main-167	bfb793f0-b117-4d15-b362-a665a30590f1	\N	\N	10000000	2	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
165	App\\Models\\User	168	اصلی	main-168	4991110f-91b6-4a29-a7f9-5e364daf349b	\N	\N	10000000	2	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
166	App\\Models\\User	169	اصلی	main-169	bd690b12-ef51-44c8-882a-8485c6fbdcd1	\N	\N	10000000	2	2025-09-08 17:04:18	2025-09-08 17:04:18	\N
167	App\\Models\\User	170	اصلی	main-170	1bdfb5c9-4087-4b0b-9182-58ed65f70df9	\N	\N	10000000	2	2025-09-08 17:04:19	2025-09-08 17:04:19	\N
\.


--
-- Name: ai_content_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ai_content_templates_id_seq', 1, false);


--
-- Name: ai_contents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ai_contents_id_seq', 1, false);


--
-- Name: ai_search_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ai_search_logs_id_seq', 22, true);


--
-- Name: ai_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ai_settings_id_seq', 1, false);


--
-- Name: api_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.api_tokens_id_seq', 1, false);


--
-- Name: auto_response_contexts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.auto_response_contexts_id_seq', 1, false);


--
-- Name: auto_response_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.auto_response_logs_id_seq', 1, false);


--
-- Name: auto_responses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.auto_responses_id_seq', 1, false);


--
-- Name: banks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.banks_id_seq', 32, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.categories_id_seq', 1, false);


--
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.comments_id_seq', 1, false);


--
-- Name: contact_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.contact_messages_id_seq', 1, false);


--
-- Name: currencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.currencies_id_seq', 3, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: filament_filter_set_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.filament_filter_set_user_id_seq', 1, false);


--
-- Name: filament_filter_sets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.filament_filter_sets_id_seq', 1, false);


--
-- Name: filament_filter_sets_managed_preset_views_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.filament_filter_sets_managed_preset_views_id_seq', 1, false);


--
-- Name: footer_contents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.footer_contents_id_seq', 9, true);


--
-- Name: footer_links_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.footer_links_id_seq', 57, true);


--
-- Name: footer_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.footer_sections_id_seq', 10, true);


--
-- Name: gateway_transaction_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.gateway_transaction_logs_id_seq', 1290, true);


--
-- Name: gateway_transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.gateway_transactions_id_seq', 430, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: media_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.media_id_seq', 58, true);


--
-- Name: meta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.meta_id_seq', 1, false);


--
-- Name: metas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.metas_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.migrations_id_seq', 85, true);


--
-- Name: otps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.otps_id_seq', 98, true);


--
-- Name: pages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.pages_id_seq', 1, false);


--
-- Name: payment_gateways_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.payment_gateways_id_seq', 5, true);


--
-- Name: payment_methods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.payment_methods_id_seq', 1, false);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.permissions_id_seq', 1, false);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: posts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.posts_id_seq', 1, false);


--
-- Name: redirects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.redirects_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.roles_id_seq', 1, true);


--
-- Name: service_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.service_categories_id_seq', 3, true);


--
-- Name: service_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.service_requests_id_seq', 319, true);


--
-- Name: service_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.service_results_id_seq', 1, false);


--
-- Name: services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.services_id_seq', 469, true);


--
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.settings_id_seq', 1, false);


--
-- Name: site_links_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.site_links_id_seq', 12, true);


--
-- Name: support_agent_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.support_agent_categories_id_seq', 1, false);


--
-- Name: support_agents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.support_agents_id_seq', 1, false);


--
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.tags_id_seq', 14, true);


--
-- Name: tax_rules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.tax_rules_id_seq', 2, true);


--
-- Name: telegram_admin_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_admin_sessions_id_seq', 1, false);


--
-- Name: telegram_admins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_admins_id_seq', 1, false);


--
-- Name: telegram_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_audit_logs_id_seq', 1, false);


--
-- Name: telegram_posts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_posts_id_seq', 1, false);


--
-- Name: telegram_security_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_security_events_id_seq', 1, false);


--
-- Name: telegram_ticket_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_ticket_messages_id_seq', 1, false);


--
-- Name: telegram_tickets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.telegram_tickets_id_seq', 1, false);


--
-- Name: ticket_activities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_activities_id_seq', 1, false);


--
-- Name: ticket_attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_attachments_id_seq', 2, true);


--
-- Name: ticket_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_categories_id_seq', 1, false);


--
-- Name: ticket_escalation_rules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_escalation_rules_id_seq', 1, false);


--
-- Name: ticket_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_messages_id_seq', 6, true);


--
-- Name: ticket_priorities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_priorities_id_seq', 1, false);


--
-- Name: ticket_sla_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_sla_settings_id_seq', 1, false);


--
-- Name: ticket_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_statuses_id_seq', 1, false);


--
-- Name: ticket_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.ticket_templates_id_seq', 1, false);


--
-- Name: tickets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.tickets_id_seq', 4, true);


--
-- Name: token_refresh_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.token_refresh_logs_id_seq', 1, false);


--
-- Name: tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.tokens_id_seq', 9, true);


--
-- Name: transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.transactions_id_seq', 167, true);


--
-- Name: transfers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.transfers_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.users_id_seq', 189, true);


--
-- Name: wallet_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.wallet_audit_logs_id_seq', 1, false);


--
-- Name: wallets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ali_master
--

SELECT pg_catalog.setval('public.wallets_id_seq', 167, true);


--
-- Name: ai_content_templates ai_content_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_content_templates
    ADD CONSTRAINT ai_content_templates_pkey PRIMARY KEY (id);


--
-- Name: ai_contents ai_contents_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_contents
    ADD CONSTRAINT ai_contents_pkey PRIMARY KEY (id);


--
-- Name: ai_contents ai_contents_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_contents
    ADD CONSTRAINT ai_contents_slug_unique UNIQUE (slug);


--
-- Name: ai_search_logs ai_search_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_search_logs
    ADD CONSTRAINT ai_search_logs_pkey PRIMARY KEY (id);


--
-- Name: ai_settings ai_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_settings
    ADD CONSTRAINT ai_settings_pkey PRIMARY KEY (id);


--
-- Name: api_tokens api_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.api_tokens
    ADD CONSTRAINT api_tokens_pkey PRIMARY KEY (id);


--
-- Name: api_tokens api_tokens_token_hash_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.api_tokens
    ADD CONSTRAINT api_tokens_token_hash_unique UNIQUE (token_hash);


--
-- Name: auto_response_contexts auto_response_contexts_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_contexts
    ADD CONSTRAINT auto_response_contexts_pkey PRIMARY KEY (id);


--
-- Name: auto_response_logs auto_response_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_logs
    ADD CONSTRAINT auto_response_logs_pkey PRIMARY KEY (id);


--
-- Name: auto_responses auto_responses_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_responses
    ADD CONSTRAINT auto_responses_pkey PRIMARY KEY (id);


--
-- Name: banks banks_bank_id_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.banks
    ADD CONSTRAINT banks_bank_id_unique UNIQUE (bank_id);


--
-- Name: banks banks_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.banks
    ADD CONSTRAINT banks_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_unique UNIQUE (slug);


--
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- Name: contact_messages contact_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.contact_messages
    ADD CONSTRAINT contact_messages_pkey PRIMARY KEY (id);


--
-- Name: currencies currencies_code_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.currencies
    ADD CONSTRAINT currencies_code_unique UNIQUE (code);


--
-- Name: currencies currencies_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.currencies
    ADD CONSTRAINT currencies_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: filament_filter_set_user filament_filter_set_user_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_set_user
    ADD CONSTRAINT filament_filter_set_user_pkey PRIMARY KEY (id);


--
-- Name: filament_filter_sets_managed_preset_views filament_filter_sets_managed_preset_views_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets_managed_preset_views
    ADD CONSTRAINT filament_filter_sets_managed_preset_views_pkey PRIMARY KEY (id);


--
-- Name: filament_filter_sets filament_filter_sets_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets
    ADD CONSTRAINT filament_filter_sets_pkey PRIMARY KEY (id);


--
-- Name: footer_contents footer_contents_key_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_contents
    ADD CONSTRAINT footer_contents_key_unique UNIQUE (key);


--
-- Name: footer_contents footer_contents_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_contents
    ADD CONSTRAINT footer_contents_pkey PRIMARY KEY (id);


--
-- Name: footer_links footer_links_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_links
    ADD CONSTRAINT footer_links_pkey PRIMARY KEY (id);


--
-- Name: footer_sections footer_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_sections
    ADD CONSTRAINT footer_sections_pkey PRIMARY KEY (id);


--
-- Name: footer_sections footer_sections_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_sections
    ADD CONSTRAINT footer_sections_slug_unique UNIQUE (slug);


--
-- Name: gateway_transaction_logs gateway_transaction_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transaction_logs
    ADD CONSTRAINT gateway_transaction_logs_pkey PRIMARY KEY (id);


--
-- Name: gateway_transactions gateway_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions
    ADD CONSTRAINT gateway_transactions_pkey PRIMARY KEY (id);


--
-- Name: gateway_transactions gateway_transactions_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions
    ADD CONSTRAINT gateway_transactions_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: media media_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_pkey PRIMARY KEY (id);


--
-- Name: media media_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_uuid_unique UNIQUE (uuid);


--
-- Name: meta meta_metable_type_metable_id_key_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.meta
    ADD CONSTRAINT meta_metable_type_metable_id_key_unique UNIQUE (metable_type, metable_id, key);


--
-- Name: meta meta_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.meta
    ADD CONSTRAINT meta_pkey PRIMARY KEY (id);


--
-- Name: metas metas_metable_id_metable_type_key_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.metas
    ADD CONSTRAINT metas_metable_id_metable_type_key_unique UNIQUE (metable_id, metable_type, key);


--
-- Name: metas metas_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.metas
    ADD CONSTRAINT metas_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: otps otps_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.otps
    ADD CONSTRAINT otps_pkey PRIMARY KEY (id);


--
-- Name: pages pages_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.pages
    ADD CONSTRAINT pages_pkey PRIMARY KEY (id);


--
-- Name: pages pages_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.pages
    ADD CONSTRAINT pages_slug_unique UNIQUE (slug);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: payment_gateways payment_gateways_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_gateways
    ADD CONSTRAINT payment_gateways_pkey PRIMARY KEY (id);


--
-- Name: payment_gateways payment_gateways_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_gateways
    ADD CONSTRAINT payment_gateways_slug_unique UNIQUE (slug);


--
-- Name: payment_methods payment_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: posts posts_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_pkey PRIMARY KEY (id);


--
-- Name: posts posts_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_slug_unique UNIQUE (slug);


--
-- Name: redirects redirects_from_url_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.redirects
    ADD CONSTRAINT redirects_from_url_unique UNIQUE (from_url);


--
-- Name: redirects redirects_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.redirects
    ADD CONSTRAINT redirects_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: service_categories service_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_categories
    ADD CONSTRAINT service_categories_pkey PRIMARY KEY (id);


--
-- Name: service_categories service_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_categories
    ADD CONSTRAINT service_categories_slug_unique UNIQUE (slug);


--
-- Name: service_requests service_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_pkey PRIMARY KEY (id);


--
-- Name: service_requests service_requests_request_hash_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_request_hash_unique UNIQUE (request_hash);


--
-- Name: service_results service_results_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results
    ADD CONSTRAINT service_results_pkey PRIMARY KEY (id);


--
-- Name: service_results service_results_result_hash_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results
    ADD CONSTRAINT service_results_result_hash_unique UNIQUE (result_hash);


--
-- Name: services services_parent_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_parent_slug_unique UNIQUE (parent_id, slug);


--
-- Name: services services_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_unique UNIQUE (key);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: site_links site_links_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.site_links
    ADD CONSTRAINT site_links_pkey PRIMARY KEY (id);


--
-- Name: support_agent_categories support_agent_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agent_categories
    ADD CONSTRAINT support_agent_categories_pkey PRIMARY KEY (id);


--
-- Name: support_agent_categories support_agent_categories_support_agent_id_ticket_category_id_un; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agent_categories
    ADD CONSTRAINT support_agent_categories_support_agent_id_ticket_category_id_un UNIQUE (support_agent_id, ticket_category_id);


--
-- Name: support_agents support_agents_agent_code_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agents
    ADD CONSTRAINT support_agents_agent_code_unique UNIQUE (agent_code);


--
-- Name: support_agents support_agents_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agents
    ADD CONSTRAINT support_agents_pkey PRIMARY KEY (id);


--
-- Name: support_agents support_agents_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agents
    ADD CONSTRAINT support_agents_user_id_unique UNIQUE (user_id);


--
-- Name: taggables taggables_tag_id_taggable_id_taggable_type_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.taggables
    ADD CONSTRAINT taggables_tag_id_taggable_id_taggable_type_unique UNIQUE (tag_id, taggable_id, taggable_type);


--
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- Name: tax_rules tax_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tax_rules
    ADD CONSTRAINT tax_rules_pkey PRIMARY KEY (id);


--
-- Name: telegram_admin_sessions telegram_admin_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admin_sessions
    ADD CONSTRAINT telegram_admin_sessions_pkey PRIMARY KEY (id);


--
-- Name: telegram_admin_sessions telegram_admin_sessions_session_token_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admin_sessions
    ADD CONSTRAINT telegram_admin_sessions_session_token_unique UNIQUE (session_token);


--
-- Name: telegram_admins telegram_admins_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admins
    ADD CONSTRAINT telegram_admins_pkey PRIMARY KEY (id);


--
-- Name: telegram_admins telegram_admins_telegram_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admins
    ADD CONSTRAINT telegram_admins_telegram_user_id_unique UNIQUE (telegram_user_id);


--
-- Name: telegram_audit_logs telegram_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_audit_logs
    ADD CONSTRAINT telegram_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: telegram_posts telegram_posts_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_posts
    ADD CONSTRAINT telegram_posts_pkey PRIMARY KEY (id);


--
-- Name: telegram_security_events telegram_security_events_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_security_events
    ADD CONSTRAINT telegram_security_events_pkey PRIMARY KEY (id);


--
-- Name: telegram_ticket_messages telegram_ticket_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_ticket_messages
    ADD CONSTRAINT telegram_ticket_messages_pkey PRIMARY KEY (id);


--
-- Name: telegram_tickets telegram_tickets_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_tickets
    ADD CONSTRAINT telegram_tickets_pkey PRIMARY KEY (id);


--
-- Name: ticket_activities ticket_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_activities
    ADD CONSTRAINT ticket_activities_pkey PRIMARY KEY (id);


--
-- Name: ticket_attachments ticket_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_attachments
    ADD CONSTRAINT ticket_attachments_pkey PRIMARY KEY (id);


--
-- Name: ticket_categories ticket_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_categories
    ADD CONSTRAINT ticket_categories_pkey PRIMARY KEY (id);


--
-- Name: ticket_categories ticket_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_categories
    ADD CONSTRAINT ticket_categories_slug_unique UNIQUE (slug);


--
-- Name: ticket_escalation_rules ticket_escalation_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules
    ADD CONSTRAINT ticket_escalation_rules_pkey PRIMARY KEY (id);


--
-- Name: ticket_messages ticket_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_messages
    ADD CONSTRAINT ticket_messages_pkey PRIMARY KEY (id);


--
-- Name: ticket_priorities ticket_priorities_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_priorities
    ADD CONSTRAINT ticket_priorities_pkey PRIMARY KEY (id);


--
-- Name: ticket_priorities ticket_priorities_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_priorities
    ADD CONSTRAINT ticket_priorities_slug_unique UNIQUE (slug);


--
-- Name: ticket_sla_settings ticket_sla_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_sla_settings
    ADD CONSTRAINT ticket_sla_settings_pkey PRIMARY KEY (id);


--
-- Name: ticket_statuses ticket_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_statuses
    ADD CONSTRAINT ticket_statuses_pkey PRIMARY KEY (id);


--
-- Name: ticket_statuses ticket_statuses_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_statuses
    ADD CONSTRAINT ticket_statuses_slug_unique UNIQUE (slug);


--
-- Name: ticket_templates ticket_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates
    ADD CONSTRAINT ticket_templates_pkey PRIMARY KEY (id);


--
-- Name: ticket_templates ticket_templates_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates
    ADD CONSTRAINT ticket_templates_slug_unique UNIQUE (slug);


--
-- Name: tickets tickets_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_pkey PRIMARY KEY (id);


--
-- Name: tickets tickets_ticket_hash_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_ticket_hash_unique UNIQUE (ticket_hash);


--
-- Name: tickets tickets_ticket_number_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_ticket_number_unique UNIQUE (ticket_number);


--
-- Name: token_refresh_logs token_refresh_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.token_refresh_logs
    ADD CONSTRAINT token_refresh_logs_pkey PRIMARY KEY (id);


--
-- Name: tokens tokens_name_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tokens
    ADD CONSTRAINT tokens_name_unique UNIQUE (name);


--
-- Name: tokens tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tokens
    ADD CONSTRAINT tokens_pkey PRIMARY KEY (id);


--
-- Name: transactions transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_pkey PRIMARY KEY (id);


--
-- Name: transactions transactions_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_uuid_unique UNIQUE (uuid);


--
-- Name: transfers transfers_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_pkey PRIMARY KEY (id);


--
-- Name: transfers transfers_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_uuid_unique UNIQUE (uuid);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_mobile_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_mobile_unique UNIQUE (mobile);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: wallet_audit_logs wallet_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallet_audit_logs
    ADD CONSTRAINT wallet_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: wallets wallets_holder_type_holder_id_slug_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT wallets_holder_type_holder_id_slug_unique UNIQUE (holder_type, holder_id, slug);


--
-- Name: wallets wallets_pkey; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT wallets_pkey PRIMARY KEY (id);


--
-- Name: wallets wallets_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT wallets_uuid_unique UNIQUE (uuid);


--
-- Name: ai_content_templates_category_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_content_templates_category_is_active_index ON public.ai_content_templates USING btree (category, is_active);


--
-- Name: ai_content_templates_usage_count_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_content_templates_usage_count_index ON public.ai_content_templates USING btree (usage_count);


--
-- Name: ai_contents_generation_progress_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_contents_generation_progress_index ON public.ai_contents USING btree (generation_progress);


--
-- Name: ai_contents_model_type_model_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_contents_model_type_model_id_index ON public.ai_contents USING btree (model_type, model_id);


--
-- Name: ai_contents_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_contents_status_index ON public.ai_contents USING btree (status);


--
-- Name: ai_search_logs_intent_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_search_logs_intent_created_at_index ON public.ai_search_logs USING btree (intent, created_at);


--
-- Name: ai_search_logs_ip_address_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_search_logs_ip_address_created_at_index ON public.ai_search_logs USING btree (ip_address, created_at);


--
-- Name: ai_search_logs_session_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_search_logs_session_id_index ON public.ai_search_logs USING btree (session_id);


--
-- Name: ai_search_logs_type_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_search_logs_type_created_at_index ON public.ai_search_logs USING btree (type, created_at);


--
-- Name: ai_search_logs_user_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ai_search_logs_user_id_created_at_index ON public.ai_search_logs USING btree (user_id, created_at);


--
-- Name: api_tokens_is_active_expires_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX api_tokens_is_active_expires_at_index ON public.api_tokens USING btree (is_active, expires_at);


--
-- Name: api_tokens_token_hash_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX api_tokens_token_hash_index ON public.api_tokens USING btree (token_hash);


--
-- Name: auto_response_contexts_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_response_contexts_is_active_index ON public.auto_response_contexts USING btree (is_active);


--
-- Name: auto_response_contexts_priority_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_response_contexts_priority_index ON public.auto_response_contexts USING btree (priority);


--
-- Name: auto_response_logs_escalated_to_support_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_response_logs_escalated_to_support_index ON public.auto_response_logs USING btree (escalated_to_support);


--
-- Name: auto_response_logs_ticket_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_response_logs_ticket_id_index ON public.auto_response_logs USING btree (ticket_id);


--
-- Name: auto_response_logs_was_helpful_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_response_logs_was_helpful_index ON public.auto_response_logs USING btree (was_helpful);


--
-- Name: auto_responses_context_id_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_responses_context_id_is_active_index ON public.auto_responses USING btree (context_id, is_active);


--
-- Name: auto_responses_language_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX auto_responses_language_index ON public.auto_responses USING btree (language);


--
-- Name: currencies_is_active_is_base_currency_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX currencies_is_active_is_base_currency_index ON public.currencies USING btree (is_active, is_base_currency);


--
-- Name: gateway_transaction_logs_action_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transaction_logs_action_created_at_index ON public.gateway_transaction_logs USING btree (action, created_at);


--
-- Name: gateway_transaction_logs_gateway_transaction_id_created_at_inde; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transaction_logs_gateway_transaction_id_created_at_inde ON public.gateway_transaction_logs USING btree (gateway_transaction_id, created_at);


--
-- Name: gateway_transaction_logs_ip_address_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transaction_logs_ip_address_index ON public.gateway_transaction_logs USING btree (ip_address);


--
-- Name: gateway_transaction_logs_source_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transaction_logs_source_index ON public.gateway_transaction_logs USING btree (source);


--
-- Name: gateway_transactions_gateway_transaction_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_gateway_transaction_id_index ON public.gateway_transactions USING btree (gateway_transaction_id);


--
-- Name: gateway_transactions_payment_gateway_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_payment_gateway_id_status_index ON public.gateway_transactions USING btree (payment_gateway_id, status);


--
-- Name: gateway_transactions_status_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_status_created_at_index ON public.gateway_transactions USING btree (status, created_at);


--
-- Name: gateway_transactions_type_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_type_status_index ON public.gateway_transactions USING btree (type, status);


--
-- Name: gateway_transactions_user_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_user_id_status_index ON public.gateway_transactions USING btree (user_id, status);


--
-- Name: gateway_transactions_uuid_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX gateway_transactions_uuid_index ON public.gateway_transactions USING btree (uuid);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: media_model_type_model_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX media_model_type_model_id_index ON public.media USING btree (model_type, model_id);


--
-- Name: media_order_column_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX media_order_column_index ON public.media USING btree (order_column);


--
-- Name: meta_key_metable_type_numeric_value_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX meta_key_metable_type_numeric_value_index ON public.meta USING btree (key, metable_type, numeric_value);


--
-- Name: metas_metable_type_metable_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX metas_metable_type_metable_id_index ON public.metas USING btree (metable_type, metable_id);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: otps_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX otps_created_at_index ON public.otps USING btree (created_at);


--
-- Name: otps_expires_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX otps_expires_at_index ON public.otps USING btree (expires_at);


--
-- Name: otps_mobile_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX otps_mobile_index ON public.otps USING btree (mobile);


--
-- Name: otps_mobile_type_is_used_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX otps_mobile_type_is_used_index ON public.otps USING btree (mobile, type, is_used);


--
-- Name: payable_confirmed_ind; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payable_confirmed_ind ON public.transactions USING btree (payable_type, payable_id, confirmed);


--
-- Name: payable_type_confirmed_ind; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payable_type_confirmed_ind ON public.transactions USING btree (payable_type, payable_id, type, confirmed);


--
-- Name: payable_type_ind; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payable_type_ind ON public.transactions USING btree (payable_type, payable_id, type);


--
-- Name: payable_type_payable_id_ind; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payable_type_payable_id_ind ON public.transactions USING btree (payable_type, payable_id);


--
-- Name: payment_gateways_is_active_is_default_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_gateways_is_active_is_default_index ON public.payment_gateways USING btree (is_active, is_default);


--
-- Name: payment_gateways_slug_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_gateways_slug_index ON public.payment_gateways USING btree (slug);


--
-- Name: payment_methods_payment_gateway_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_methods_payment_gateway_id_index ON public.payment_methods USING btree (payment_gateway_id);


--
-- Name: payment_methods_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_methods_type_index ON public.payment_methods USING btree (type);


--
-- Name: payment_methods_user_id_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_methods_user_id_is_active_index ON public.payment_methods USING btree (user_id, is_active);


--
-- Name: payment_methods_user_id_is_default_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX payment_methods_user_id_is_default_index ON public.payment_methods USING btree (user_id, is_default);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: redirects_from_url_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX redirects_from_url_index ON public.redirects USING btree (from_url);


--
-- Name: redirects_is_active_from_url_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX redirects_is_active_from_url_index ON public.redirects USING btree (is_active, from_url);


--
-- Name: service_requests_request_hash_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_requests_request_hash_index ON public.service_requests USING btree (request_hash);


--
-- Name: service_requests_service_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_requests_service_id_status_index ON public.service_requests USING btree (service_id, status);


--
-- Name: service_requests_user_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_requests_user_id_status_index ON public.service_requests USING btree (user_id, status);


--
-- Name: service_requests_wallet_transaction_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_requests_wallet_transaction_id_index ON public.service_requests USING btree (wallet_transaction_id);


--
-- Name: service_results_result_hash_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_result_hash_index ON public.service_results USING btree (result_hash);


--
-- Name: service_results_service_id_processed_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_service_id_processed_at_index ON public.service_results USING btree (service_id, processed_at);


--
-- Name: service_results_status_processed_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_status_processed_at_index ON public.service_results USING btree (status, processed_at);


--
-- Name: service_results_user_id_processed_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_user_id_processed_at_index ON public.service_results USING btree (user_id, processed_at);


--
-- Name: service_results_user_id_service_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_user_id_service_id_index ON public.service_results USING btree (user_id, service_id);


--
-- Name: service_results_wallet_transaction_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX service_results_wallet_transaction_id_index ON public.service_results USING btree (wallet_transaction_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: settings_group_sort_order_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX settings_group_sort_order_index ON public.settings USING btree ("group", sort_order);


--
-- Name: settings_is_public_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX settings_is_public_index ON public.settings USING btree (is_public);


--
-- Name: support_agents_is_active_auto_assign_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX support_agents_is_active_auto_assign_index ON public.support_agents USING btree (is_active, auto_assign);


--
-- Name: support_agents_is_online_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX support_agents_is_online_index ON public.support_agents USING btree (is_online);


--
-- Name: taggables_taggable_type_taggable_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX taggables_taggable_type_taggable_id_index ON public.taggables USING btree (taggable_type, taggable_id);


--
-- Name: tax_rules_is_active_is_default_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tax_rules_is_active_is_default_index ON public.tax_rules USING btree (is_active, is_default);


--
-- Name: tax_rules_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tax_rules_type_index ON public.tax_rules USING btree (type);


--
-- Name: telegram_admin_sessions_admin_id_expires_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_admin_sessions_admin_id_expires_at_index ON public.telegram_admin_sessions USING btree (admin_id, expires_at);


--
-- Name: telegram_admin_sessions_expires_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_admin_sessions_expires_at_index ON public.telegram_admin_sessions USING btree (expires_at);


--
-- Name: telegram_admin_sessions_session_token_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_admin_sessions_session_token_index ON public.telegram_admin_sessions USING btree (session_token);


--
-- Name: telegram_admins_role_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_admins_role_is_active_index ON public.telegram_admins USING btree (role, is_active);


--
-- Name: telegram_admins_telegram_user_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_admins_telegram_user_id_index ON public.telegram_admins USING btree (telegram_user_id);


--
-- Name: telegram_audit_logs_action_success_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_audit_logs_action_success_index ON public.telegram_audit_logs USING btree (action, success);


--
-- Name: telegram_audit_logs_admin_id_action_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_audit_logs_admin_id_action_index ON public.telegram_audit_logs USING btree (admin_id, action);


--
-- Name: telegram_audit_logs_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_audit_logs_created_at_index ON public.telegram_audit_logs USING btree (created_at);


--
-- Name: telegram_posts_created_by_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_posts_created_by_index ON public.telegram_posts USING btree (created_by);


--
-- Name: telegram_posts_scheduled_for_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_posts_scheduled_for_index ON public.telegram_posts USING btree (scheduled_for);


--
-- Name: telegram_posts_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_posts_status_index ON public.telegram_posts USING btree (status);


--
-- Name: telegram_security_events_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_security_events_created_at_index ON public.telegram_security_events USING btree (created_at);


--
-- Name: telegram_security_events_event_type_severity_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_security_events_event_type_severity_index ON public.telegram_security_events USING btree (event_type, severity);


--
-- Name: telegram_security_events_telegram_user_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_security_events_telegram_user_id_index ON public.telegram_security_events USING btree (telegram_user_id);


--
-- Name: telegram_ticket_messages_is_admin_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_ticket_messages_is_admin_index ON public.telegram_ticket_messages USING btree (is_admin);


--
-- Name: telegram_ticket_messages_ticket_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_ticket_messages_ticket_id_created_at_index ON public.telegram_ticket_messages USING btree (ticket_id, created_at);


--
-- Name: telegram_ticket_messages_user_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_ticket_messages_user_id_created_at_index ON public.telegram_ticket_messages USING btree (user_id, created_at);


--
-- Name: telegram_ticket_messages_user_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_ticket_messages_user_id_index ON public.telegram_ticket_messages USING btree (user_id);


--
-- Name: telegram_tickets_assigned_to_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_assigned_to_index ON public.telegram_tickets USING btree (assigned_to);


--
-- Name: telegram_tickets_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_created_at_index ON public.telegram_tickets USING btree (created_at);


--
-- Name: telegram_tickets_priority_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_priority_index ON public.telegram_tickets USING btree (priority);


--
-- Name: telegram_tickets_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_status_index ON public.telegram_tickets USING btree (status);


--
-- Name: telegram_tickets_status_updated_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_status_updated_at_index ON public.telegram_tickets USING btree (status, updated_at);


--
-- Name: telegram_tickets_user_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_user_id_index ON public.telegram_tickets USING btree (user_id);


--
-- Name: telegram_tickets_user_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX telegram_tickets_user_id_status_index ON public.telegram_tickets USING btree (user_id, status);


--
-- Name: ticket_activities_action_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_activities_action_index ON public.ticket_activities USING btree (action);


--
-- Name: ticket_activities_ticket_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_activities_ticket_id_created_at_index ON public.ticket_activities USING btree (ticket_id, created_at);


--
-- Name: ticket_attachments_ticket_id_ticket_message_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_attachments_ticket_id_ticket_message_id_index ON public.ticket_attachments USING btree (ticket_id, ticket_message_id);


--
-- Name: ticket_categories_is_active_sort_order_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_categories_is_active_sort_order_index ON public.ticket_categories USING btree (is_active, sort_order);


--
-- Name: ticket_escalation_rules_is_active_sort_order_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_escalation_rules_is_active_sort_order_index ON public.ticket_escalation_rules USING btree (is_active, sort_order);


--
-- Name: ticket_messages_is_auto_response_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_messages_is_auto_response_index ON public.ticket_messages USING btree (is_auto_response);


--
-- Name: ticket_messages_is_internal_is_system_message_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_messages_is_internal_is_system_message_index ON public.ticket_messages USING btree (is_internal, is_system_message);


--
-- Name: ticket_messages_message_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_messages_message_type_index ON public.ticket_messages USING btree (message_type);


--
-- Name: ticket_messages_ticket_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_messages_ticket_id_created_at_index ON public.ticket_messages USING btree (ticket_id, created_at);


--
-- Name: ticket_priorities_is_active_level_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_priorities_is_active_level_index ON public.ticket_priorities USING btree (is_active, level);


--
-- Name: ticket_sla_settings_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_sla_settings_is_active_index ON public.ticket_sla_settings USING btree (is_active);


--
-- Name: ticket_statuses_is_active_sort_order_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_statuses_is_active_sort_order_index ON public.ticket_statuses USING btree (is_active, sort_order);


--
-- Name: ticket_statuses_is_default_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_statuses_is_default_index ON public.ticket_statuses USING btree (is_default);


--
-- Name: ticket_templates_is_active_category_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_templates_is_active_category_id_index ON public.ticket_templates USING btree (is_active, category_id);


--
-- Name: ticket_templates_is_public_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX ticket_templates_is_public_index ON public.ticket_templates USING btree (is_public);


--
-- Name: tickets_assigned_to_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_assigned_to_status_index ON public.tickets USING btree (assigned_to, status);


--
-- Name: tickets_category_id_status_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_category_id_status_id_index ON public.tickets USING btree (category_id, status_id);


--
-- Name: tickets_escalation_count_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_escalation_count_index ON public.tickets USING btree (escalation_count);


--
-- Name: tickets_is_auto_responded_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_is_auto_responded_index ON public.tickets USING btree (is_auto_responded);


--
-- Name: tickets_priority_id_status_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_priority_id_status_id_index ON public.tickets USING btree (priority_id, status_id);


--
-- Name: tickets_status_priority_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_status_priority_index ON public.tickets USING btree (status, priority);


--
-- Name: tickets_ticket_hash_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_ticket_hash_index ON public.tickets USING btree (ticket_hash);


--
-- Name: tickets_ticket_number_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_ticket_number_index ON public.tickets USING btree (ticket_number);


--
-- Name: tickets_user_id_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tickets_user_id_status_index ON public.tickets USING btree (user_id, status);


--
-- Name: token_refresh_logs_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX token_refresh_logs_created_at_index ON public.token_refresh_logs USING btree (created_at);


--
-- Name: token_refresh_logs_provider_status_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX token_refresh_logs_provider_status_index ON public.token_refresh_logs USING btree (provider, status);


--
-- Name: token_refresh_logs_started_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX token_refresh_logs_started_at_index ON public.token_refresh_logs USING btree (started_at);


--
-- Name: token_refresh_logs_status_trigger_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX token_refresh_logs_status_trigger_type_index ON public.token_refresh_logs USING btree (status, trigger_type);


--
-- Name: tokens_expires_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tokens_expires_at_index ON public.tokens USING btree (expires_at);


--
-- Name: tokens_provider_is_active_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX tokens_provider_is_active_index ON public.tokens USING btree (provider, is_active);


--
-- Name: transactions_payable_type_payable_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX transactions_payable_type_payable_id_index ON public.transactions USING btree (payable_type, payable_id);


--
-- Name: transactions_type_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX transactions_type_index ON public.transactions USING btree (type);


--
-- Name: transfers_from_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX transfers_from_id_index ON public.transfers USING btree (from_id);


--
-- Name: transfers_to_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX transfers_to_id_index ON public.transfers USING btree (to_id);


--
-- Name: users_mobile_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX users_mobile_index ON public.users USING btree (mobile);


--
-- Name: value_string_prefix_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX value_string_prefix_index ON public.meta USING btree (metable_type, key, substr(value, 1, 255));


--
-- Name: wallet_audit_logs_admin_id_action_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX wallet_audit_logs_admin_id_action_index ON public.wallet_audit_logs USING btree (admin_id, action);


--
-- Name: wallet_audit_logs_wallet_id_created_at_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX wallet_audit_logs_wallet_id_created_at_index ON public.wallet_audit_logs USING btree (wallet_id, created_at);


--
-- Name: wallets_holder_type_holder_id_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX wallets_holder_type_holder_id_index ON public.wallets USING btree (holder_type, holder_id);


--
-- Name: wallets_slug_index; Type: INDEX; Schema: public; Owner: ali_master
--

CREATE INDEX wallets_slug_index ON public.wallets USING btree (slug);


--
-- Name: ai_content_templates ai_content_templates_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_content_templates
    ADD CONSTRAINT ai_content_templates_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.telegram_admins(id) ON DELETE CASCADE;


--
-- Name: ai_search_logs ai_search_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ai_search_logs
    ADD CONSTRAINT ai_search_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: api_tokens api_tokens_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.api_tokens
    ADD CONSTRAINT api_tokens_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.telegram_admins(id) ON DELETE SET NULL;


--
-- Name: auto_response_logs auto_response_logs_context_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_logs
    ADD CONSTRAINT auto_response_logs_context_id_foreign FOREIGN KEY (context_id) REFERENCES public.auto_response_contexts(id) ON DELETE SET NULL;


--
-- Name: auto_response_logs auto_response_logs_response_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_logs
    ADD CONSTRAINT auto_response_logs_response_id_foreign FOREIGN KEY (response_id) REFERENCES public.auto_responses(id) ON DELETE SET NULL;


--
-- Name: auto_response_logs auto_response_logs_ticket_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_response_logs
    ADD CONSTRAINT auto_response_logs_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES public.tickets(id) ON DELETE CASCADE;


--
-- Name: auto_responses auto_responses_context_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.auto_responses
    ADD CONSTRAINT auto_responses_context_id_foreign FOREIGN KEY (context_id) REFERENCES public.auto_response_contexts(id) ON DELETE CASCADE;


--
-- Name: comments comments_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.comments(id) ON DELETE CASCADE;


--
-- Name: comments comments_post_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_post_id_foreign FOREIGN KEY (post_id) REFERENCES public.posts(id) ON DELETE CASCADE;


--
-- Name: filament_filter_set_user filament_filter_set_user_filter_set_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_set_user
    ADD CONSTRAINT filament_filter_set_user_filter_set_id_foreign FOREIGN KEY (filter_set_id) REFERENCES public.filament_filter_sets(id) ON DELETE CASCADE;


--
-- Name: filament_filter_set_user filament_filter_set_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_set_user
    ADD CONSTRAINT filament_filter_set_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: filament_filter_sets_managed_preset_views filament_filter_sets_managed_preset_views_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets_managed_preset_views
    ADD CONSTRAINT filament_filter_sets_managed_preset_views_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filament_filter_sets filament_filter_sets_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.filament_filter_sets
    ADD CONSTRAINT filament_filter_sets_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: footer_links footer_links_footer_section_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.footer_links
    ADD CONSTRAINT footer_links_footer_section_id_foreign FOREIGN KEY (footer_section_id) REFERENCES public.footer_sections(id) ON DELETE CASCADE;


--
-- Name: gateway_transaction_logs gateway_transaction_logs_gateway_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transaction_logs
    ADD CONSTRAINT gateway_transaction_logs_gateway_transaction_id_foreign FOREIGN KEY (gateway_transaction_id) REFERENCES public.gateway_transactions(id) ON DELETE CASCADE;


--
-- Name: gateway_transactions gateway_transactions_currency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions
    ADD CONSTRAINT gateway_transactions_currency_id_foreign FOREIGN KEY (currency_id) REFERENCES public.currencies(id) ON DELETE RESTRICT;


--
-- Name: gateway_transactions gateway_transactions_payment_gateway_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions
    ADD CONSTRAINT gateway_transactions_payment_gateway_id_foreign FOREIGN KEY (payment_gateway_id) REFERENCES public.payment_gateways(id) ON DELETE RESTRICT;


--
-- Name: gateway_transactions gateway_transactions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.gateway_transactions
    ADD CONSTRAINT gateway_transactions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: payment_methods payment_methods_payment_gateway_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_payment_gateway_id_foreign FOREIGN KEY (payment_gateway_id) REFERENCES public.payment_gateways(id) ON DELETE CASCADE;


--
-- Name: payment_methods payment_methods_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: posts posts_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: posts posts_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: redirects redirects_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.redirects
    ADD CONSTRAINT redirects_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: service_requests service_requests_service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_service_id_foreign FOREIGN KEY (service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: service_requests service_requests_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: service_requests service_requests_wallet_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_wallet_transaction_id_foreign FOREIGN KEY (wallet_transaction_id) REFERENCES public.transactions(id) ON DELETE SET NULL;


--
-- Name: service_results service_results_service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results
    ADD CONSTRAINT service_results_service_id_foreign FOREIGN KEY (service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: service_results service_results_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results
    ADD CONSTRAINT service_results_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: service_results service_results_wallet_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.service_results
    ADD CONSTRAINT service_results_wallet_transaction_id_foreign FOREIGN KEY (wallet_transaction_id) REFERENCES public.transactions(id) ON DELETE SET NULL;


--
-- Name: services services_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: services services_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.service_categories(id) ON DELETE CASCADE;


--
-- Name: services services_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.services(id) ON DELETE SET NULL;


--
-- Name: support_agent_categories support_agent_categories_support_agent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agent_categories
    ADD CONSTRAINT support_agent_categories_support_agent_id_foreign FOREIGN KEY (support_agent_id) REFERENCES public.support_agents(id) ON DELETE CASCADE;


--
-- Name: support_agent_categories support_agent_categories_ticket_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agent_categories
    ADD CONSTRAINT support_agent_categories_ticket_category_id_foreign FOREIGN KEY (ticket_category_id) REFERENCES public.ticket_categories(id) ON DELETE CASCADE;


--
-- Name: support_agents support_agents_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.support_agents
    ADD CONSTRAINT support_agents_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: taggables taggables_tag_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.taggables
    ADD CONSTRAINT taggables_tag_id_foreign FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- Name: telegram_admin_sessions telegram_admin_sessions_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_admin_sessions
    ADD CONSTRAINT telegram_admin_sessions_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.telegram_admins(id) ON DELETE CASCADE;


--
-- Name: telegram_audit_logs telegram_audit_logs_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_audit_logs
    ADD CONSTRAINT telegram_audit_logs_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.telegram_admins(id) ON DELETE SET NULL;


--
-- Name: telegram_posts telegram_posts_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_posts
    ADD CONSTRAINT telegram_posts_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.telegram_admins(id) ON DELETE CASCADE;


--
-- Name: telegram_posts telegram_posts_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_posts
    ADD CONSTRAINT telegram_posts_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.telegram_admins(id) ON DELETE SET NULL;


--
-- Name: telegram_security_events telegram_security_events_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_security_events
    ADD CONSTRAINT telegram_security_events_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.telegram_admins(id) ON DELETE SET NULL;


--
-- Name: telegram_ticket_messages telegram_ticket_messages_ticket_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.telegram_ticket_messages
    ADD CONSTRAINT telegram_ticket_messages_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES public.telegram_tickets(id) ON DELETE CASCADE;


--
-- Name: ticket_activities ticket_activities_ticket_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_activities
    ADD CONSTRAINT ticket_activities_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES public.tickets(id) ON DELETE CASCADE;


--
-- Name: ticket_activities ticket_activities_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_activities
    ADD CONSTRAINT ticket_activities_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: ticket_attachments ticket_attachments_ticket_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_attachments
    ADD CONSTRAINT ticket_attachments_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES public.tickets(id) ON DELETE CASCADE;


--
-- Name: ticket_attachments ticket_attachments_ticket_message_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_attachments
    ADD CONSTRAINT ticket_attachments_ticket_message_id_foreign FOREIGN KEY (ticket_message_id) REFERENCES public.ticket_messages(id) ON DELETE CASCADE;


--
-- Name: ticket_categories ticket_categories_auto_assign_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_categories
    ADD CONSTRAINT ticket_categories_auto_assign_to_foreign FOREIGN KEY (auto_assign_to) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: ticket_escalation_rules ticket_escalation_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules
    ADD CONSTRAINT ticket_escalation_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.ticket_categories(id) ON DELETE CASCADE;


--
-- Name: ticket_escalation_rules ticket_escalation_rules_escalate_to_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules
    ADD CONSTRAINT ticket_escalation_rules_escalate_to_priority_id_foreign FOREIGN KEY (escalate_to_priority_id) REFERENCES public.ticket_priorities(id) ON DELETE SET NULL;


--
-- Name: ticket_escalation_rules ticket_escalation_rules_escalate_to_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules
    ADD CONSTRAINT ticket_escalation_rules_escalate_to_user_id_foreign FOREIGN KEY (escalate_to_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: ticket_escalation_rules ticket_escalation_rules_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_escalation_rules
    ADD CONSTRAINT ticket_escalation_rules_priority_id_foreign FOREIGN KEY (priority_id) REFERENCES public.ticket_priorities(id) ON DELETE CASCADE;


--
-- Name: ticket_messages ticket_messages_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_messages
    ADD CONSTRAINT ticket_messages_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.ticket_templates(id) ON DELETE SET NULL;


--
-- Name: ticket_messages ticket_messages_ticket_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_messages
    ADD CONSTRAINT ticket_messages_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES public.tickets(id) ON DELETE CASCADE;


--
-- Name: ticket_messages ticket_messages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_messages
    ADD CONSTRAINT ticket_messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: ticket_priorities ticket_priorities_escalate_to_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_priorities
    ADD CONSTRAINT ticket_priorities_escalate_to_priority_id_foreign FOREIGN KEY (escalate_to_priority_id) REFERENCES public.ticket_priorities(id) ON DELETE SET NULL;


--
-- Name: ticket_sla_settings ticket_sla_settings_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_sla_settings
    ADD CONSTRAINT ticket_sla_settings_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.ticket_categories(id) ON DELETE CASCADE;


--
-- Name: ticket_sla_settings ticket_sla_settings_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_sla_settings
    ADD CONSTRAINT ticket_sla_settings_priority_id_foreign FOREIGN KEY (priority_id) REFERENCES public.ticket_priorities(id) ON DELETE CASCADE;


--
-- Name: ticket_templates ticket_templates_auto_change_status_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates
    ADD CONSTRAINT ticket_templates_auto_change_status_to_foreign FOREIGN KEY (auto_change_status_to) REFERENCES public.ticket_statuses(id) ON DELETE SET NULL;


--
-- Name: ticket_templates ticket_templates_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates
    ADD CONSTRAINT ticket_templates_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.ticket_categories(id) ON DELETE SET NULL;


--
-- Name: ticket_templates ticket_templates_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.ticket_templates
    ADD CONSTRAINT ticket_templates_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: tickets tickets_assigned_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_assigned_to_foreign FOREIGN KEY (assigned_to) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_auto_response_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_auto_response_id_foreign FOREIGN KEY (auto_response_id) REFERENCES public.auto_responses(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.ticket_categories(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_escalated_from_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_escalated_from_priority_id_foreign FOREIGN KEY (escalated_from_priority_id) REFERENCES public.ticket_priorities(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_priority_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_priority_id_foreign FOREIGN KEY (priority_id) REFERENCES public.ticket_priorities(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.ticket_statuses(id) ON DELETE SET NULL;


--
-- Name: tickets tickets_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: transactions transactions_wallet_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_wallet_id_foreign FOREIGN KEY (wallet_id) REFERENCES public.wallets(id) ON DELETE CASCADE;


--
-- Name: transfers transfers_deposit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_deposit_id_foreign FOREIGN KEY (deposit_id) REFERENCES public.transactions(id) ON DELETE CASCADE;


--
-- Name: transfers transfers_withdraw_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_withdraw_id_foreign FOREIGN KEY (withdraw_id) REFERENCES public.transactions(id) ON DELETE CASCADE;


--
-- Name: wallet_audit_logs wallet_audit_logs_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ali_master
--

ALTER TABLE ONLY public.wallet_audit_logs
    ADD CONSTRAINT wallet_audit_logs_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.telegram_admins(id) ON DELETE SET NULL;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT CREATE ON SCHEMA public TO ali_master;


--
-- PostgreSQL database dump complete
--

\unrestrict G9rVtFzJgEnkTpsGSyDW1lMfDqO0znZDiHMTgPdfWaK6ldsFtkjnbiiQ9XPkZVh

